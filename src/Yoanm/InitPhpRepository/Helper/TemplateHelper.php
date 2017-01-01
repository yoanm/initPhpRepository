<?php
namespace Yoanm\InitPhpRepository\Helper;

use Symfony\Component\Filesystem\Filesystem;
use Yoanm\InitPhpRepository\Resolver\NamespaceResolver;

/**
 * Class TemplateFileProcessor
 */
class TemplateHelper
{
    /** @var \Twig_Environment */
    private $twig;
    /** @var Filesystem */
    private $fileSystem;
    /** @var string */
    private static $templateBasePath = null;

    /**
     * @param \Twig_Environment $twig
     * @param array             $varList
     */
    public function __construct(\Twig_Environment $twig, array $varList)
    {
        $this->twig = $twig;
        $this->fileSystem = new Filesystem();

        $this->initTwig($varList);
    }

    /**
     * @param string $template
     * @param string $outputFilePath
     */
    public function dump($template, $outputFilePath)
    {
        $this->fileSystem->dumpFile(
            $outputFilePath,
            $this->twig->render($template)
        );
    }

    public static function getTemplateBasePath()
    {
        if (null === self::$templateBasePath) {
            self::$templateBasePath = realpath(sprintf('%s/../../../../templates', __DIR__));
        }
        return self::$templateBasePath;
    }

    /**
     * @param array $varList
     *
     * @throws \Twig_Error_Loader
     */
    private function initTwig(array $varList)
    {
        $loader = new \Twig_Loader_Filesystem();
        $this->twig->setLoader($loader);

        // Set template namespaces
        $loader->addPath(sprintf('%s/%s', self::getTemplateBasePath(), 'base'));
        $loader->addPath(
            sprintf('%s/%s', self::getTemplateBasePath(), '/override/library/php'),
            NamespaceResolver::LIBRARY_NAMESPACE
        );
        $loader->addPath(
            sprintf('%s/%s', self::getTemplateBasePath(), '/override/library/symfony'),
            NamespaceResolver::SYMFONY_LIBRARY_NAMESPACE
        );
        $loader->addPath(
            sprintf('%s/%s', self::getTemplateBasePath(), '/override/project/php'),
            NamespaceResolver::PROJECT_NAMESPACE
        );

        // define variable as global
        $twigVarList = [];
        // merge keys as array
        foreach ($varList as $varName => $varValue) {
            $twigVarList = array_merge_recursive($twigVarList, $this->resolveVar($varName, $varValue));
        }
        foreach ($twigVarList as $key => $val) {
            $this->twig->addGlobal($key, $val);
        }
    }

    /**
     * @param string $varName
     * @param string $varValue
     *
     * @return array
     */
    protected function resolveVar($varName, $varValue)
    {
        $componentList = explode('.', $varName);
        if (count($componentList) > 1) {
            $varName = array_shift($componentList);
            $varValue = $this->resolveVar(implode('.', $componentList), $varValue);
        }

        return [$varName => $varValue];
    }
}
