<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;

/**
 * Class TemplateFileProcessor
 */
class TemplateFileProcessor
{
    /** @var TemplateHelper */
    private $helper;

    /**
     * TemplateFileProcessor constructor.
     * @param TemplateHelper $helper
     */
    public function __construct(TemplateHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param string $templateFilePath
     * @param string $outputDir
     */
    public function process($templateFilePath, $outputDir = '.')
    {
        $this->getHelper()->dumpTemplate(
            $templateFilePath,
            $this->getHelper()->resolveOutputFilePath($templateFilePath, $outputDir)
        );
    }

    /**
     * @return TemplateHelper
     */
    protected function getHelper()
    {
        return $this->helper;
    }
}
