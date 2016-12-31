<?php
namespace Yoanm\DefaultPhpRepository\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yoanm\DefaultPhpRepository\Command\Helper\CommandTemplateHelper;
use Yoanm\DefaultPhpRepository\Command\Processor\CommandTemplateProcessor;
use Yoanm\DefaultPhpRepository\Factory\TemplatePathBagFactory;
use Yoanm\DefaultPhpRepository\Factory\VariableBagFactory;

class InitCommand extends Command
{
    const TYPE_INIT = 'template.init';
    const TYPE_GIT = 'template.git';
    const TYPE_COMPOSER = 'template.composer';
    const TYPE_TEST = 'template.test';
    const TYPE_CI = 'template.ci';

    const OUTPUT_LEVEL_SPACE = '    ';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Will init the current github repository with default file templates')
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'type of repository (library/symfony/project)',
                Mode::PHP_LIBRARY
            )
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List template file instead of creation them')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'process only given ids'
            )
            ->addOption(
                'ask-before-override',
                'a',
                InputOption::VALUE_NONE,
                'Will ask before overriding an existing file'
            )
            ->addOption('force-override', 'f', InputOption::VALUE_NONE, 'Override existing files by default')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $input->getArgument('type');

        if (!$this->modeIsValid($output, $mode)) {
            return 1;
        }

        $skipExistingFile = false === $input->getOption('ask-before-override');
        $forceOverride = $input->getOption('force-override');
        if (true === $forceOverride) {
            $skipExistingFile = false;
        }

        $variableBag = (new VariableBagFactory())->load($mode);
        $templatePathList = (new TemplatePathBagFactory())->load($mode);

        $commandTemplateHelper = new CommandTemplateHelper(
            $this->getHelper('question'),
            $input,
            $output,
            $variableBag->all(),
            $skipExistingFile,
            $forceOverride
        );
        $commandProcessor = new CommandTemplateProcessor($commandTemplateHelper);

        $output->writeln(sprintf('<comment>Creating default files for : </comment><info>%s</info>', ucwords($mode)));
        if (true === $forceOverride) {
            $output->writeln('<fg=red>WARNING :  Existing files will be overriden by default</fg=red>');
        } elseif (true === $skipExistingFile) {
            $output->writeln('<comment>INFO : Existing files will be skipped !</comment>');
        }
        try {
            $currentType = null;
            foreach ($templatePathList as $templateKey => $templatePath) {
                if (count($input->getOption('id')) && !in_array($templateKey, $input->getOption('id'))) {
                    continue;
                }

                if (null === $currentType || !preg_match(sprintf('#%s#', preg_quote($currentType)), $templateKey)) {
                    preg_match('#(template\.[^\.]+)#', $templateKey, $matches);
                    $currentType = isset($matches[1]) ? $matches[1] : $templateKey;
                    $header = ucwords(str_replace('template.', '', $currentType));
                    if ('Init' === $header) {
                        $header = 'Init repository';
                    } elseif ('Ci' === $header) {
                        $header = 'Continuous integration';
                    }
                    $output->writeln(sprintf('<info>%s%s</info>', self::OUTPUT_LEVEL_SPACE, $header));
                }

                $output->writeln(sprintf(
                    '%s* %s : ',
                    str_repeat(self::OUTPUT_LEVEL_SPACE, 2),
                    ucwords(str_replace('template.', '', str_replace($currentType.'.', '', $templateKey)))
                ));
                if (true === $input->getOption('list')) {
                    $output->writeln(sprintf(
                        '%s<comment>Id   : </comment><info>%s</info>',
                        str_repeat(self::OUTPUT_LEVEL_SPACE, 3),
                        $templateKey
                    ));
                    $output->writeln(sprintf(
                        '%s<comment>File : </comment><info>%s</info>',
                        str_repeat(self::OUTPUT_LEVEL_SPACE, 3),
                        $templatePath
                    ));
                } else {
                    $commandProcessor->process($templatePath);
                }
            }
            return 0;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error -> %s</error>', $e->getMessage()));
            throw $e;
        }
    }

    protected function modeIsValid(OutputInterface $output, $mode)
    {
        $availableModeList = Mode::all();
        if (!in_array($mode, $availableModeList)) {
            $output->writeln(sprintf('<error>Unexpected mode "%s" !</error>', $mode));
            $output->writeln(sprintf(
                '<info>Allowed mode : %s </info>',
                implode(
                    ' / ',
                    array_map(
                        function ($availableMode) {
                            return sprintf('<comment>%s</comment>', $availableMode);
                        },
                        $availableModeList
                    )
                )
            ));

            return false;
        }

        return true;
    }
}
