<?php
namespace Yoanm\InitPhpRepository\Model;

/**
 * Class Template
 */
class Template
{
    /** @var string */
    private $id;
    /** @var string */
    private $target;
    /** @var string */
    private $source;
    /** @var string */
    private $namespace = null;

    /**
     * @param string $id
     * @param string $source
     * @param string $target
     */
    public function __construct($id, $source, $target)
    {
        $this->id = $id;
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * @param string $namespace
     *
     * @return Template
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
