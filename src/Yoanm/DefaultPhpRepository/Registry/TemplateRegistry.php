<?php
namespace Yoanm\DefaultPhpRepository\Registry;

use Yoanm\DefaultPhpRepository\Command\Mode;
use Yoanm\DefaultPhpRepository\Helper\PathHelper;

/**
 * Class TemplateRegistry
 */
class TemplateRegistry
{
    private $rootTemplatePath;

    public function __construct()
    {
        $this->rootTemplatePath = PathHelper::appendPathSeparator(
            sprintf(
                '%s%s%s',
                PathHelper::appendPathSeparator(__DIR__),
                PathHelper::implodePathComponentList(array_fill(0, 4, '..')), // Go 4 level up
                'templates'
            )
        );
    }

    /**
     * @param string $templateName
     *
     * @return string the template path
     *
     * @throws \Exception if template not found
     */
    public function getTemplatePath($templateName)
    {
        $filename = sprintf(
            '%s%s',
            $this->rootTemplatePath,
            sprintf('%s.tmpl', $templateName)
        );

        if (!file_exists($filename)) {
            throw new \Exception(sprintf('template "%s" not found !', $filename));
        }

        return realpath($filename);
    }
}
