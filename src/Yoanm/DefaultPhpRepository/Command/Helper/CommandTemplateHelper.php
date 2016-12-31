<?php
namespace Yoanm\DefaultPhpRepository\Command\Helper;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Yoanm\DefaultPhpRepository\Exception\TargetFileExistsException;
use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;

/**
 * Class CommandTemplateHelper
 */
class CommandTemplateHelper extends TemplateHelper
{
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var Filesystem */
    private $fileSystem;
    /** @var bool */
    private $skipExisting;
    /** @var bool */
    private $forceOverride;
    /** @var QuestionHelper */
    private $questionHelper;

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $variableList
     * @param bool            $skipExisting
     * @param bool            $forceOverride
     * @param array           $extraTemplatePath
     */
    public function __construct(
        QuestionHelper $questionHelper,
        InputInterface $input,
        OutputInterface $output,
        array $variableList,
        $skipExisting = true,
        $forceOverride = false,
        array $extraTemplatePath = []
    ) {
        parent::__construct($variableList, $extraTemplatePath);

        $this->questionHelper = $questionHelper;
        $this->fileSystem = new Filesystem();
        $this->input = $input;
        $this->output = $output;

        $this->skipExisting = $skipExisting;
        $this->forceOverride = $forceOverride;
    }

    /**
     * @param string $templateFilePath
     * @param string $outputFilePath
     * @param bool   $overwrite overwrite even if present
     *
     * @throws TargetFileExistsException
     */
    public function dumpTemplate($templateFilePath, $outputFilePath, $overwrite = false)
    {
        $this->output->write("            <info>$outputFilePath</info> : ");
        $fileExist = $this->fileSystem->exists($outputFilePath);
        $process = true;
        if ($fileExist) {
            if (false === $this->forceOverride && true === $this->skipExisting) {
                $this->output->writeln('<comment>Skipped !</comment>');
                $process = false;
            } else {
                $process = false;
                if (true === $this->forceOverride) {
                    $process = true;
                    $this->output->writeln('<comment>Overriden !</comment>');
                } elseif ($this->doOverwrite()) {
                    $process = true;
                }
            }
        }
        if (true === $process) {
            parent::dumpTemplate($templateFilePath, $outputFilePath);
            if (false === $fileExist) {
                $this->output->writeln('<info>Done</info>');
            }
        }
    }

    /**@return bool
     */
    protected function doOverwrite()
    {
        $question = new ConfirmationQuestion(
            '<question>Overwrite (y/n) ? [n]</question>',
            false
        );

        return $this->questionHelper->ask($this->input, $this->output, $question);
    }
}
