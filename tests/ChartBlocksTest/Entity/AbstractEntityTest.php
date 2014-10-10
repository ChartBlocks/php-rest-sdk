<?php

namespace ChartBlocksTest;

use ChartBlocks\Entity\AbstractEntity;

class AbstractEntityTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \ChartBlocks\Entity\AbstractEntity
     */
    protected $entity;

    /**
     *
     * @var \ChartBlocks\Repository\AbstractRepository
     */
    protected $repo;

    /**
     * @var array
     */
    protected $data;

    public function setUp() {
        $this->repo = $this->getMock('\ChartBlocks\Repository\AbstractRepository', array(), array(), '', false);
        $this->data = array('name' => 'Qwijibo');
        $this->entity = $this->getMockForAbstractClass('\ChartBlocks\Entity\AbstractEntity', array($this->repo, $this->data));
    }

    public function testConstructSetsRepository() {
        $this->assertSame($this->repo, $this->entity->getRepository());
    }

    public function testSetData() {
        $mock = $this->getMockBuilder('\ChartBlocks\Entity\AbstractEntity')
                ->setMethods(array('__set'))
                ->disableOriginalConstructor()
                ->getMock();

        $mock->expects($this->exactly(2))
                ->method('__set')
                ->withConsecutive(array('name', 'Qwijibo'), array('active', true));

        $result = $mock->setData(array('name' => 'Qwijibo', 'active' => true));
        $this->assertSame($result, $mock, 'setData should return self');
    }

    /**
     * @depends testSetData
     */
    public function testConstructSetsData() {
        $this->assertSame($this->data['name'], $this->entity->name);
    }

    /**
     * @depends testSetData
     */
    public function testToArray() {
        $data = array(
            'name' => 'Qwijibo',
            'active' => true,
            'classTest' => new \stdClass
        );

        $this->entity->setData($data);
        $this->assertEquals($data, $this->entity->toArray());
    }

    public function testGetId() {
        $mock = $this->getMockBuilder('\ChartBlocks\Entity\AbstractEntity')
                ->setMethods(array('retrieve'))
                ->disableOriginalConstructor()
                ->getMock();

        $mock->expects($this->once())
                ->method('retrieve')
                ->with('id')
                ->will($this->returnValue('_anID_'));

        $id = $mock->getId();
        $this->assertEquals('_anID_', $id);
    }

    /**
     * @depends testGetId
     */
    public function testGettingAPropertyWithMethod() {
        $mock = $this->getMockBuilder('\ChartBlocks\Entity\AbstractEntity')
                ->setMethods(array('getId'))
                ->disableOriginalConstructor()
                ->getMock();

        $mock->expects($this->once())
                ->method('getId')
                ->will($this->returnValue('_anID_'));

        $id = $mock->id;
        $this->assertEquals('_anID_', $id);
    }

    /**
     * @depends testGetId
     */
    public function testSettingAPropertyWithMethod() {
        $mock = $this->getMockBuilder('\ChartBlocks\Entity\AbstractEntity')
                ->setMethods(array('setAccount'))
                ->disableOriginalConstructor()
                ->getMock();

        $mock->expects($this->once())
                ->method('setAccount')
                ->with('an account')
                ->will($this->returnSelf());

        $mock->account = 'an account';
    }

    /**
     * @depends testConstructSetsData
     */
    public function testIssetingAProperty() {
        $this->assertFalse(isset($this->entity->qwijibo), 'Qwijibo should not be set');
        $this->assertTrue(isset($this->entity->name), 'Name should be set');
    }

    /**
     * @depends testConstructSetsData
     */
    public function testCallingGetOnAPropertyWhereMethodDoesNotExist() {
        $this->assertEquals($this->data['name'], $this->entity->getName());
    }

    public function testCallingSetOnAPropertyWhereMethodDoesNotExist() {
        $name = 'John';
        $this->entity->setName('John');

        $this->assertEquals($name, $this->entity->getName());
    }

    /**
     * @expectedException \ChartBlocks\Entity\Exception
     */
    public function testCallingAMethodThatWontExist() {
        $this->entity->qwijibo();
    }

    public function testGetEntityFactory() {
        $client = $this->getMock('\ChartBlocks\Client');
        $this->entity->getRepository()->expects($this->once())
                ->method('getClient')
                ->will($this->returnValue($client));
        
        $this->assertInstanceOf('\ChartBlocks\Entity\EntityFactory', $this->entity->getEntityFactory());
    }

}
