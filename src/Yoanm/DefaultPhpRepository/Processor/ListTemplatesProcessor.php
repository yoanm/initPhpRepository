<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Yoanm\DefaultPhpRepository\Helper\CommandHelper;
use Yoanm\DefaultPhpRepository\Model\FolderTemplate;
use Yoanm\DefaultPhpRepository\Model\Template;
use Yoanm\DefaultPhpRepository\Resolver\NamespaceResolver;

/**
 * Class ListTemplatesProcessor
 */
class ListTemplatesProcessor
{
    /** @var CommandHelper */
    private $helper;

    /**
     * @param CommandHelper $helper
     */
    public function __construct(CommandHelper $helper)
    {
        $this->helper = $helper;
    }

    public function process()
    {
        $currentType = null;
        foreach ($this->helper->getTemplateList() as $template) {
            $this->helper->displayHeader($template, $currentType);

            $currentType = $this->helper->resolveCurrentType($template);

            if ($template instanceof FolderTemplate) {
                $this->displayFolderTitle($template);
                $this->displayTemplateInfo($template);
            } else {
                $this->displayFileTitle($template);
                $this->displayTemplateInfo($template);
            }
        }
    }

    /**
     * @param Template $template
     */
    protected function displayTemplateInfo(Template $template)
    {
        $this->helper->display(sprintf(
            ' - %s/%s',
            $this->resolveTemplatePath($template),
            $template->getSource()
        ));
    }

    /**
     * @param Template $template
     */
    protected function displayFolderTitle(Template $template)
    {
        $this->helper->display(
            sprintf('<comment>%s : </comment><info>Folder</info>', $template->getId()),
            2,
            false
        );
    }

    /**
     * @param Template $template
     */
    protected function displayFileTitle(Template $template)
    {
        $this->helper->display(
            sprintf('<comment>%s : </comment><info>File</info>', $template->getId()),
            2,
            false
        );
    }

    protected function resolveTemplatePath(Template $template)
    {
        $basePath = 'templates';
        $path = sprintf('%s/base', $basePath);
        if (NamespaceResolver::SYMFONY_LIBRARY_NAMESPACE === $template->getNamespace()) {
            $path = sprintf('%s/override/library/symfony', $basePath);
        } elseif (NamespaceResolver::LIBRARY_NAMESPACE === $template->getNamespace()) {
            $path = sprintf('%s/override/library/php', $basePath);
        }

        return $path;
    }
}
