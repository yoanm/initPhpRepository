<?php
namespace Yoanm\DefaultPhpRepository\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yoanm\DefaultPhpRepository\Factory\TemplatePathListFactory;
use Yoanm\DefaultPhpRepository\Factory\VariableBagFactory;
use Yoanm\DefaultPhpRepository\Processor\TemplateProcessor;

class InitCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Will init the current github repository with default file templates');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $variableBag = (new VariableBagFactory())->load();
        $templatePathList = (new TemplatePathListFactory())->load();

        $processor = new TemplateProcessor($variableBag);

        foreach ($templatePathList as $templateKey => $templatePath) {
            $output->writeln(sprintf('<info>Creataing %s file</info>', $templateKey));
            $processor->process($templatePath);
        }
    }
}
