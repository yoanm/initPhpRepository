<?php
namespace Yoanm\DefaultPhpRepository\Exception;

/**
 * Class Mode
 */
class TargetFileExistsException extends \Exception
{
    /** @var string */
    private $targetFilePath;
    /** @var string */
    private $templateFilePath;

    /**
     * @param string $targetFilePath
     * @param string $templateFilePath
     */
    public function __construct($targetFilePath, $templateFilePath)
    {
        parent::__construct(sprintf('"%s" already exists !', $targetFilePath));

        $this->targetFilePath = $targetFilePath;
        $this->templateFilePath = $templateFilePath;
    }

    /**
     * @return string
     */
    public function getTargetFilePath()
    {
        return $this->targetFilePath;
    }

    /**
     * @return string
     */
    public function getTemplateFilePath()
    {
        return $this->templateFilePath;
    }
}
