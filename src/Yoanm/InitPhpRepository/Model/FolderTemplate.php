<?php
namespace Yoanm\InitPhpRepository\Model;

/**
 * Class FolderTemplate
 */
class FolderTemplate extends Template
{
    /** @var Template[] */
    private $fileList = [];

    /**
     * @param string $id
     * @param string $path
     */
    public function __construct($id, $path)
    {
        parent::__construct($id, $path, $path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getId();
    }

    /**
     * @param Template $fileTemplate
     */
    public function addFile(Template $fileTemplate)
    {
        // Override namespace
        $fileTemplate->setNamespace($this->getNamespace());
        $this->fileList[$fileTemplate->getId()] = $fileTemplate;
    }

    /**
     * @return Template[]
     */
    public function getFileList()
    {
        return $this->fileList;
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        //override files namespace too
        foreach ($this->getFileList() as $fileTemplate) {
            $fileTemplate->setNamespace($namespace);
        }

        return parent::setNamespace($namespace);
    }
}
