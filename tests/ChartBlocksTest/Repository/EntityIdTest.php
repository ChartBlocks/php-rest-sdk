<?php

namespace ChartBlocksTest;

use ChartBlocks\Entity\EntityId;

class EntityIdTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \ChartBlocks\Entity\EntityId
     */
    protected $entityid;

    public function setUp() {
        $this->entityid = new EntityId();
    }

    public function testValidIds() {
        $ids = array(
            '5431f8cac9a61d3a19d2d259',
            '54315308c9a61dff0cd2d259'
        );

        foreach ($ids as $id) {
            $this->assertTrue($this->entityid->isValid($id));
        }
    }

    public function testInvalidIds() {
        $ids = array(
            1,
            '1',
            '54315308c9a61dff0cd2d25', // 23 chars,
            '!4315308c9a61dff0cd2d259', // invalid character
        );

        foreach ($ids as $id) {
            $this->assertFalse($this->entityid->isValid($id));
        }
    }

}
