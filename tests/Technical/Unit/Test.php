<?php
namespace Technical\Unit\Yoanm\DefaultPhpRepository;

class Test extends \PHPUnit_Framework_TestCase
{
    /** @var TESTED_CLASS */
    private $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->object = new TESTED_CLASS();
    }
}
