<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Yoanm\DefaultPhpRepository\Helper\PathHelper;

/**
 * Class TemplateProcessor
 */
class TemplateProcessor
{
    /** @var array */
    private $variableList = [];
    /** @var string[] */
    private $variableNameList = [];
    /** @var string */
    private $filenameResolverRegexp;
    /** @var Filesystem */
    private $fs;

    /**
     * @param array $variableList
     * @param array $extraTemplatePath
     */
    public function __construct(array $variableList, array $extraTemplatePath = [])
    {
        $this->variableList = $variableList;
        foreach($this->variableList as $variableId => $variableValue) {
            $variableId = sprintf('%%%s%%', $variableId);
            $this->variableNameList[$variableId] = $variableId;
        }

        foreach ($extraTemplatePath as $extraKey => $extraValue) {
            $variableId = sprintf('%%%s%%', $extraKey);
            $this->variableNameList[$variableId] = $variableId;
            $this->variableList[$variableId] = $this->loadTemplate($extraValue);
        }

        $this->fs = new Filesystem();

        // compile this regexp at startup (no need to to it each time)
        $this->filenameResolverRegexp = sprintf(
            '#%s?(?:[^%s]+%s)*([^%s]+)\.tmpl$#',
            PathHelper::separator(),
            PathHelper::separator(),
            PathHelper::separator(),
            PathHelper::separator()
        );
    }

    /**
     * @param string $templatePath
     * @param string $outputDir
     *
     * @return ParameterBag
     */
    public function process($templatePath, $outputDir = '.')
    {
        $filename = sprintf(
            '%s%s',
            PathHelper::appendPathSeparator($outputDir),
            $this->resolveOutputFilename($templatePath)
        );
        $this->fs->dumpFile($filename, $this->loadTemplate($templatePath));
    }

    /**
     * @param string $templatePath
     *
     * @return string
     */
    protected function resolveOutputFilename($templatePath)
    {
        return preg_replace($this->filenameResolverRegexp, '\1', $templatePath);
    }

    /**
     * @param string $templatePath
     *
     * @return string file content
     */
    protected function loadTemplate($templatePath)
    {
        return str_replace($this->variableNameList, $this->variableList, file_get_contents($templatePath));
    }
}
