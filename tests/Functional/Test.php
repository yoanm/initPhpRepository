<?php
namespace Functional\Yoanm\InitPhpRepository;

class Test extends \PHPUnit_Framework_TestCase
{
    /** @var \stdClass */
    private $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->object = new \stdClass();
    }
}
