<?php
namespace Yoanm\DefaultPhpRepository\Registry;

use Yoanm\DefaultPhpRepository\Helper\PathHelper;

/**
 * Class TemplateRegistry
 */
class TemplateRegistry
{
    private static $rootTemplateDir = null;

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
            self::getRootTemplateDir(),
            $templateName
        );

        if (!file_exists($filename)) {
            throw new \Exception(sprintf('template "%s" not found !', $filename));
        }

        return realpath($filename);
    }

    public static function getRootTemplateDir()
    {
        if (null === self::$rootTemplateDir) {
            self::$rootTemplateDir = PathHelper::appendPathSeparator(
                realpath(
                    sprintf(
                        '%s%s%s',
                        PathHelper::appendPathSeparator(__DIR__),
                        PathHelper::implodePathComponentList(array_fill(0, 4, '..')), // Go 4 level up
                        'templates'
                    )
                )
            );
        }
        return self::$rootTemplateDir;
    }
}
