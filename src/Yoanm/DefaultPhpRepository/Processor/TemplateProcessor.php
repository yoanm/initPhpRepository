<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;

/**
 * Class TemplateProcessor
 */
class TemplateProcessor
{
    /** @var TemplateFileProcessor */
    private $templateFileProcessor;
    /** @var TemplateFolderProcessor */
    private $templateFolderProcessor;

    /**
     * @param TemplateHelper $templateHelper
     */
    public function __construct(TemplateHelper $templateHelper)
    {
        $this->templateFileProcessor = new TemplateFileProcessor($templateHelper);
        $this->templateFolderProcessor = new TemplateFolderProcessor($templateHelper);
    }

    /**
     * @param string $templatePath
     * @param string $outputDir
     */
    public function process($templatePath, $outputDir = '.')
    {
        if (is_dir($templatePath)) {
            $this->templateFolderProcessor->process($templatePath, $outputDir);
        } else {
            $this->templateFileProcessor->process($templatePath, $outputDir);
        }
    }
}
