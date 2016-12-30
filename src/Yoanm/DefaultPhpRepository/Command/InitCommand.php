<?php
namespace Yoanm\DefaultPhpRepository\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

        $output->writeln(sprintf('<comment>Creating default file for : </comment><info>%s</info>', ucwords($mode)));
        try {
            $currentType = null;
            foreach ($templatePathList as $templateKey => $templatePath) {
                if (null === $currentType || !preg_match(sprintf('#%s#', preg_quote($currentType)), $templateKey)) {
                    preg_match('#(template\.[^\.]+)#', $templateKey, $matches);
                    $currentType = isset($matches[1]) ? $matches[1] : $templateKey;
                    $header = ucwords(str_replace('template.', '', $currentType));
                    if ('Init' === $header) {
                        $header = 'Init repository';
                    } elseif ('Ci' === $header) {
                        $header = 'Continuous integration';
                    }
                    $output->writeln(sprintf(
                        '<comment>    %s</comment>',
                        $header
                    ));
                }

                $output->write(sprintf(
                    '<comment>        %s : </comment>',
                    ucwords(str_replace('template.', '', str_replace($currentType.'.', '', $templateKey)))
                ));
                $processor->process($templatePath);
                $output->writeln('<info>Done</info>');
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error -></error>%s', $e->getMessage()));
        }
    }
}
