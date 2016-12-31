<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Yoanm\DefaultPhpRepository\Model\FolderTemplate;
use Yoanm\DefaultPhpRepository\Model\Template;
use Yoanm\DefaultPhpRepository\Resolver\NamespaceResolver;

/**
 * Class ListCommandProcessor
 */
class ListCommandProcessor extends CommandProcessor
{
    public function process()
    {
        $currentType = null;
        foreach ($this->getTemplateList() as $template) {
            $this->displayHeader($template, $currentType);

            $currentType = $this->resolveCurrentType($template);

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
        $this->display(sprintf(
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
        $this->display(
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
        $this->display(
            sprintf('<comment>%s : </comment><info>File</info>', $template->getId()),
            2,
            false
        );
    }

    protected function resolveTemplatePath(Template $template)
    {
        $basePath = 'templates';
        if (NamespaceResolver::SYMFONY_LIBRARY_NAMESPACE === $template->getNamespace()) {
            return sprintf('%s/override/library/symfony', $basePath);
        } elseif (NamespaceResolver::LIBRARY_NAMESPACE === $template->getNamespace()) {
            sprintf('%s/override/library/php', $basePath);
        }

        return sprintf('%s/base', $basePath);
    }
}
