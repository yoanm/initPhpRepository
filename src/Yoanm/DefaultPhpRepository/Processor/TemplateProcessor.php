<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Yoanm\DefaultPhpRepository\Helper\PathHelper;

/**
 * Class TemplateProcessor
 */
class TemplateProcessor
{
    /** @var ParameterBag */
    private $variableBag;
    /** @var Filesystem */
    private $fs;

    /**
     * @param ParameterBag $variableBag
     */
    public function __construct(ParameterBag $variableBag)
    {
        $this->variableBag = $variableBag;
        $this->fs = new Filesystem();
    }

    /**
     * @param string $mode
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
        $filename = preg_replace(
            sprintf(
                '#%s?(?:[^%s]+%s)*([^%s]+)\.tmpl$#',
                PathHelper::separator(),
                PathHelper::separator(),
                PathHelper::separator(),
                PathHelper::separator()
            ),
            '\1',
            $templatePath
        );

        return $filename;
    }

    protected function loadTemplate($templatePath)
    {
        $variableList = $this->variableBag->all();

        $template = str_replace(
            array_map(
                function ($value) {
                    return sprintf('%%%s%%', $value);
                },
                array_keys($variableList)
            ),
            $variableList,
            file_get_contents($templatePath)
        );

        return $template;
    }
}
