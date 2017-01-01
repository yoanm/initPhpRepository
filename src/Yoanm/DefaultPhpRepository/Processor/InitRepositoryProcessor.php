<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\Console\Question\ConfirmationQuestion;
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
     */
    public function __construct(
        CommandHelper $helper,
        $skipExisting = true,
        $forceOverride = false,
        $idList = []
    ) {
        $this->helper = $helper;

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

            if ($template instanceof FolderTemplate) {

                $targetExist = false;
                $process = false;

                foreach ($template->getFileList() as $subTemplate) {
                    $targetExist = $this->helper->targetExist($subTemplate);
                    $process = $this->processOrNot($targetExist);
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
                $targetExist = $this->helper->targetExist($template);
                if (true === $this->processOrNot($targetExist)) {
                    $this->helper->dump($template);
                }
            }
            if (false === $targetExist) {
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
     * @param bool     $targetExist
     *
     * @return bool
     */
    protected function processOrNot($targetExist)
    {
        $process = true;
        if ($targetExist) {
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

        return $process;
    }
}
