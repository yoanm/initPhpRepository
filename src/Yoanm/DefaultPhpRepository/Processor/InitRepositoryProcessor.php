<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Yoanm\DefaultPhpRepository\Helper\CommandHelper;
use Yoanm\DefaultPhpRepository\Model\FolderTemplate;
use Yoanm\DefaultPhpRepository\Model\Template;

/**
 * Class InitRepositoryProcessor
 */
class InitRepositoryProcessor
{
    /** @var CommandHelper */
    private $helper;
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
     * @param CommandHelper $helper
     * @param bool          $skipExisting
     * @param bool          $forceOverride
     * @param array         $idList
     * @param string        $rootPath
     */
    public function __construct(
        CommandHelper $helper,
        $skipExisting = true,
        $forceOverride = false,
        $idList = [],
        $rootPath = '.'
    ) {
        $this->helper = $helper;

        $this->rootPath = realpath($rootPath);
        $this->fileSystem = new Filesystem();

        $this->skipExisting = $skipExisting;
        $this->forceOverride = $forceOverride;
        $this->idList = $idList;
    }

    public function process()
    {
        $currentType = null;
        foreach ($this->helper->getTemplateList() as $template) {
            if (count($this->idList) && !in_array($template->getId(), $this->idList)) {
                continue;
            }
            $this->helper->displayHeader($template, $currentType);

            $currentType = $this->helper->resolveCurrentType($template);

            $this->displayTemplate($template);
            $fileExist = false;
            if ($template instanceof FolderTemplate) {
                $process = false;
                foreach ($template->getFileList() as $subTemplate) {
                    list($fileExist, $process) = $this->checkBeforeDump($subTemplate);

                    if (false === $process) {
                        break;
                    }
                }
                if (true === $process) {
                    foreach ($template->getFileList() as $subTemplate) {
                        $this->helper->dump($subTemplate);
                    }
                }
            } else {
                list($fileExist, $process) = $this->checkBeforeDump($template);

                if (true === $process) {
                    $this->helper->dump($template);
                }
            }
            if (false === $fileExist) {
                $this->helper->display('<info>Done</info>');
            }
        }
    }

    /**
     * @param Template $template
     */
    protected function displayTemplate(Template $template)
    {
        $this->helper->display(sprintf('<comment>%s</comment>', $template->getId()), 2, false);
        $this->helper->display(sprintf(' - <info>./%s</info> : ', $template->getTarget()), 0, false);
    }

    /**
     * @param Template $template
     *
     * @return array
     */
    protected function checkBeforeDump(Template $template)
    {
        $fileExist = $this->fileSystem->exists($this->resolveTargetPath($template));
        $process = true;
        if ($fileExist) {
            if (false === $this->forceOverride && true === $this->skipExisting) {
                $this->helper->display('<comment>Skipped !</comment>');
                $process = false;
            } else {
                $process = false;
                if (true === $this->forceOverride) {
                    $process = true;
                    $this->helper->display('<comment>Overriden !</comment>');
                } elseif (
                    $this->helper->ask(
                        new ConfirmationQuestion('<question>Overwrite ? [n]</question>', false)
                    )
                ) {
                    $process = true;
                }
            }
        }

        return [$fileExist, $process];
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
