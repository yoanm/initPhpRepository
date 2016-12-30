<?php
namespace Yoanm\DefaultPhpRepository\Registry;

use Yoanm\DefaultPhpRepository\Command\Mode;
use Yoanm\DefaultPhpRepository\Helper\PathHelper;

/**
 * Class TemplateRegistry
 */
class TemplateRegistry
{
    /**
     * @param string $templateName
     * @param string $mode
     *
     * @return string
     */
    public function getTemplatePathFor($templateName, $mode)
    {
        $path = false;

        static $specialTemplateList = [
            'README.md' => true,
            '.gitignore' => true,
            'composer.json' => true,
            '.scrutinizer.yml' => true,
            '.travis.yml' => true,
        ];

        if (isset($specialTemplateList[$templateName])) {
            static $libraryOrTemplateTemplateList = [
                '.gitignore' => true,
                'composer.json' => true,
                '.scrutinizer.yml' => true,
                '.travis.yml' => true,
            ];

            if ('README.md' === $templateName) {
                switch ($mode) {
                    case Mode::PHP_LIBRARY:
                        $path = $this->getPathForDefaultLibraryTemplate($templateName);
                        break;
                    case Mode::SYMFONY_LIBRARY:
                        $path = $this->getPathForSymfonyLibraryTemplate($templateName);
                        break;
                    case Mode::PROJECT:
                        $path = $this->getPathForProjectTemplate($templateName);
                        break;
                }
            } elseif (isset($libraryOrTemplateTemplateList[$templateName])) {
                if (Mode::PROJECT === $mode) {
                    $path = $this->getPathForProjectTemplate($templateName);
                } else {
                    $path = $this->getPathForDefaultLibraryTemplate($templateName);
                }
            }
        } else {
            $path = $this->getPathForDefaultTemplate($templateName);
        }

        if (false === $path) {
            throw new \InvalidArgumentException('template not not handled for current mode!');
        }

        return $path;
    }

    /**
     * @param string $templateName
     *
     * @return string the template path
     */
    public function getPathForDefaultTemplate($templateName)
    {
        return $this->appendToRootTemplatePath(['default'], sprintf('%s.tmpl', $templateName));
    }

    /**
     * @param string $templateName
     *
     * @return string the template path
     */
    public function getPathForDefaultLibraryTemplate($templateName)
    {
        return $this->appendToRootTemplatePath(['library', 'default'], sprintf('%s.tmpl', $templateName));
    }

    /**
     * @param string $templateName
     *
     * @return string the template path
     */
    public function getPathForSymfonyLibraryTemplate($templateName)
    {
        return $this->appendToRootTemplatePath(['library', 'symfony'], sprintf('%s.tmpl', $templateName));
    }

    /**
     * @param string $templateName
     *
     * @return string the template path
     */
    public function getPathForProjectTemplate($templateName)
    {
        return $this->appendToRootTemplatePath(['project'], sprintf('%s.tmpl', $templateName));
    }

    /**
     * @param array  $subPathList
     * @param string $templateName
     *
     * @return string
     *
     * @throws \Exception
     */
    public function appendToRootTemplatePath(array $subPathList, $templateName)
    {
        $filename = sprintf(
            '%s%s%s',
            $this->appendToRootPath('templates'),
            PathHelper::implodePathComponentList($subPathList),
            $templateName
        );

        if (!file_exists($filename)) {
            throw new \Exception(sprintf('template "%s" not found !'));
        }

        return $filename;
    }

    /**
     * @param string $subPath
     *
     * @return string
     */
    protected function appendToRootPath($subPath)
    {
        return sprintf(
            '%s%s',
            sprintf(
                '%s%s',
                PathHelper::appendPathSeparator(__DIR__),
                PathHelper::implodePathComponentList(array_fill(0, 4, '..')) // Go 4 level up
            ),
            PathHelper::appendPathSeparator($subPath)
        );
    }
}
