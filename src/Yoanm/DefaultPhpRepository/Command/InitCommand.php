<?php
namespace Yoanm\DefaultPhpRepository\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yoanm\DefaultPhpRepository\Factory\TemplatePathListFactory;
use Yoanm\DefaultPhpRepository\Factory\VariableBagFactory;
use Yoanm\DefaultPhpRepository\Processor\TemplateProcessor;

class InitCommand extends Command
{
    const TYPE_INIT = 'template.init';
    const TYPE_GIT = 'template.git';
    const TYPE_COMPOSER = 'template.composer';
    const TYPE_TEST = 'template.test';
    const TYPE_CI = 'template.ci';

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
            ->addOption('id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'process only given ids')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $input->getArgument('type');
        
        $variableBag = (new VariableBagFactory())->load();
        $templatePathList = (new TemplatePathListFactory())->load($mode);

        $processor = new TemplateProcessor($variableBag);

        $outputLevelSpace = '    ';

        $output->writeln(sprintf('<comment>Creating default file for : </comment><info>%s</info>', ucwords($mode)));
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
                    $output->writeln(sprintf('%s%s', $outputLevelSpace, $header));
                }

                $output->write(sprintf(
                    '%s%s : ',
                    str_repeat($outputLevelSpace, 2),
                    ucwords(str_replace('template.', '', str_replace($currentType.'.', '', $templateKey)))
                ));
                if (true === $input->getOption('list')) {
                    $output->writeln('');
                    $output->writeln(sprintf(
                        '%s<comment>Id</comment> <info>%s</info>',
                        str_repeat($outputLevelSpace, 3),
                        $templateKey
                    ));
                    $output->writeln(sprintf(
                        '%s<comment>File</comment> <info>%s</info>',
                        str_repeat($outputLevelSpace, 3),
                        $templatePath
                    ));
                } else {
                    $processor->process($templatePath);
                    $output->writeln('<info>Done</info>');
                }
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error -></error>%s', $e->getMessage()));
        }
    }
}
