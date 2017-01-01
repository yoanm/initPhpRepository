<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;
use Yoanm\DefaultPhpRepository\Model\FolderTemplate;
use Yoanm\DefaultPhpRepository\Model\Template;

/**
 * Class InitCommandProcessor
 */
class InitCommandProcessor extends CommandProcessor
{
    /** @var TemplateHelper */
    private $templateHelper;
    /** @var Filesystem */
    private $fileSystem;
    /** @var string */
    private $rootPath;
    /** @var bool */
    private $skipExisting;
    /** @var bool */
    private $forceOverride;
    /** @var array */
    private $idList = [];

    /**
     * @param QuestionHelper     $questionHelper
     * @param InputInterface     $input
     * @param OutputInterface    $output
     * @param TemplateHelper     $templateHelper
     * @param Template[]         $templateList
     * @param bool               $skipExisting
     * @param bool               $forceOverride
     * @param array              $idList
     */
    public function __construct(
        QuestionHelper $questionHelper,
        InputInterface $input,
        OutputInterface $output,
        TemplateHelper $templateHelper,
        array $templateList,
        $skipExisting = true,
        $forceOverride = false,
        $idList = [],
        $rootPath = '.'
    ) {
        parent::__construct($questionHelper, $input, $output, $templateList);

        $this->templateHelper = $templateHelper;
        $this->rootPath = realpath($rootPath);
        $this->fileSystem = new Filesystem();

        $this->skipExisting = $skipExisting;
        $this->forceOverride = $forceOverride;
        $this->idList = $idList;
    }

    public function process()
    {
        $currentType = null;
        foreach ($this->getTemplateList() as $template) {
            if (count($this->idList) && !in_array($template->getId(), $this->idList)) {
                continue;
            }
            $this->displayHeader($template, $currentType);

            $currentType = $this->resolveCurrentType($template);
            $this->displayTemplate($template);
            $fileExist = false;
            if ($template instanceof FolderTemplate) {
                $process = false;
                foreach ($template->getFileList() as $subTemplate) {
                    list($fileExist, $process) = $this->validateDump($subTemplate);

                    if (false === $process) {
                        break;
                    }
                }
                if (true === $process) {
                    foreach ($template->getFileList() as $subTemplate) {
                        $this->dumpTemplate($subTemplate);
                    }
                }
            } else {
                list($fileExist, $process) = $this->validateDump($template);

                if (true === $process) {
                    $this->dumpTemplate($template);
                }
            }
            if (false === $fileExist) {
                $this->display('<info>Done</info>');
            }
        }
    }

    /**
     * @param Template $template
     */
    protected function displayTemplate(Template $template)
    {
        $this->display(sprintf('<comment>%s</comment>', $template->getId()), 2, false);
        $targetRelativePath = sprintf('./%s', $template->getTarget());
        $this->display(sprintf(' - <info>%s</info> : ', $targetRelativePath), 0, false);
    }

    /**
     * @param Template $template
     * @param string           $path
     */
    protected function dumpTemplate(Template $template)
    {
        $templateId = $template->getSource();
        if (null !== $template->getNamespace()) {
            $templateId = sprintf('@%s/%s', $template->getNamespace(), $template->getSource());
        }
        $this->templateHelper->dumpTemplate($templateId, $this->resolveTargetPath($template));
    }

    /**
     * @param string $target
     * @return array
     */
    protected function validateDump(Template $template)
    {
        $fileExist = $this->fileSystem->exists($this->resolveTargetPath($template));
        $process = true;
        if ($fileExist) {
            if (false === $this->forceOverride && true === $this->skipExisting) {
                $this->display('<comment>Skipped !</comment>');
                $process = false;
            } else {
                $process = false;
                if (true === $this->forceOverride) {
                    $this->display('<comment>Overriden !</comment>');
                    $process = true;
                } elseif ($this->doOverwrite()) {
                    $process = true;
                }
            }
        }

        return [$fileExist, $process];
    }

    /**@return bool
     */
    protected function doOverwrite()
    {
        return $this->ask(new ConfirmationQuestion('<question>Overwrite ? [n]</question>', false));
    }

    /**
     * @param Template $template
     *
     * @return string
     */
    protected function resolveTargetPath(Template $template)
    {
        return sprintf('%s/%s', $this->rootPath, $template->getTarget());
    }
}
