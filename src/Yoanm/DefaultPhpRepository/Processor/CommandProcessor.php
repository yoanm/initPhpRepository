<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Yoanm\DefaultPhpRepository\Model\Template;

/**
 * Class CommandProcessor
 */
class CommandProcessor
{
    const OUTPUT_LEVEL_SPACE = '    ';

    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var QuestionHelper */
    private $questionHelper;
    /** @var Template[] */
    private $templateList;

    /**
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Template[]      $templateList
     */
    public function __construct(
        QuestionHelper $questionHelper,
        InputInterface $input,
        OutputInterface $output,
        array $templateList
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->templateList = $templateList;
    }

    /**
     * @return Template[]
     */
    public function getTemplateList()
    {
        return $this->templateList;
    }

    /**
     * @param Template $template
     * @param string           $currentType
     */
    public function displayHeader(Template $template, $currentType)
    {
        if (null === $currentType || !preg_match(sprintf('#^%s\.#', preg_quote($currentType)), $template->getId())) {
            $header = ucwords($this->resolveCurrentType($template));

            if ('Git' === $header) {
                $header = 'Git/Github';
            } elseif ('Ci' === $header) {
                $header = 'Continuous integration';
            }

            $this->display(sprintf('<info>%s :</info>', $header), 1);
        }
    }

    /**
     * @param string $message
     * @param int    $level
     */
    public function display($message, $level = 0, $ln = true)
    {
        $message = sprintf(
            '%s%s',
            str_repeat(self::OUTPUT_LEVEL_SPACE, $level),
            $message
        );
        if (true === $ln) {
            $this->output->writeln($message);
        } else {
            $this->output->write($message);
        }
    }

    /**
     * @param Template $template
     *
     * @return string
     */
    public function resolveCurrentType(Template $template)
    {
        $currentType = preg_replace('#^([^\.]+)\..*#', '\1', $template->getId());

        return $currentType;
    }

    /**
     * @param Question $question
     *
     * @return string
     */
    public function ask(Question $question)
    {
        return $this->questionHelper->ask($this->input, $this->output, $question);
    }
}
