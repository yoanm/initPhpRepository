<?php
namespace Yoanm\DefaultPhpRepository\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yoanm\DefaultPhpRepository\Factory\TemplateListFactory;
use Yoanm\DefaultPhpRepository\Factory\VarFactory;
use Yoanm\DefaultPhpRepository\Helper\CommandHelper;
use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;
use Yoanm\DefaultPhpRepository\Processor\InitRepositoryProcessor;
use Yoanm\DefaultPhpRepository\Processor\ListTemplatesProcessor;

class InitCommand extends Command
{
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
                'Repository type (library/project)',
                RepositoryType::PROJECT
            )
            ->addOption(
                'symfony',
                null,
                InputOption::VALUE_NONE,
                'If symfony sub type'
            )
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'List template ids')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'process only given templates ids'
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
        $repositoryType = $input->getArgument('type');
        $repositorySubType = true === $input->getOption('symfony')
            ? RepositorySubType::SYMFONY
            : RepositorySubType::PHP
        ;

        if (!$this->validateRepositoryType($output, $repositoryType)) {
            return 1;
        }

        $this->getProcessor($input, $output, $repositoryType, $repositorySubType)->process();
    }

    /**
     * @param OutputInterface $output
     * @param string          $repositoryType
     *
     * @return bool
     */
    protected function validateRepositoryType(OutputInterface $output, $repositoryType)
    {
        $availableTypeList = RepositoryType::all();
        if (!in_array($repositoryType, $availableTypeList)) {
            $output->writeln(sprintf('<error>Unexpected type "%s" !</error>', $repositoryType));
            $output->writeln(sprintf(
                '<info>Allowed type : %s </info>',
                implode(' / ', array_map(function ($availableMode) {
                            return sprintf('<comment>%s</comment>', $availableMode);
                        },
                        $availableTypeList
                    )
                )
            ));

            return false;
        }

        return true;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $repositoryType
     * @param string          $repositorySubType
     *
     * @return InitRepositoryProcessor|ListTemplatesProcessor
     *
     * @throws \Twig_Error_Loader
     */
    protected function getProcessor(InputInterface $input, OutputInterface $output, $repositoryType, $repositorySubType)
    {
        $forceOverride = $input->getOption('force-override');
        $skipExisting = true === $forceOverride
            ? false
            : false === $input->getOption('ask-before-override')
        ;

        $helper = new CommandHelper(
            $input,
            $output,
            new TemplateHelper(
                new \Twig_Environment(null, ['autoescape' => false]),
                (new VarFactory())->create(RepositoryType::PROJECT === $repositoryType)
            ),
            $this->getHelper('question'),
            (new TemplateListFactory())->create($repositoryType, $repositorySubType)
        );

        if (true === $input->getOption('list')) {
            $processor = new ListTemplatesProcessor($helper);
        } else {
            $processor = new InitRepositoryProcessor(
                $helper,
                $skipExisting,
                $forceOverride,
                $input->getOption('id')
            );
        }

        return $processor;
    }
}
