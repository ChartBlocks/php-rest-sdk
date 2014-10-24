<?php

namespace ChartBlocksTest;

use ChartBlocks\Entity\EntityFactory;

class EntityFactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \ChartBlocks\Client
     */
    protected $client;

    public function setUp() {
        $this->client = $this->getMock('\ChartBlocks\Client');
    }

    public function testGivenActualClass() {
        $chart = $this->getMockBuilder('\ChartBlocks\Entity\Chart')
                ->disableOriginalConstructor()
                ->getMock();

        $factory = new EntityFactory($this->client);
        $result = $factory->createInstanceOf('Chart', $chart);

        $this->assertSame($chart, $result);
    }

    public function testGivenArrayOfData() {
        $data = array(
            'name' => 'Qwijibo'
        );

        $repository = $this->getMockBuilder('\ChartBlocks\Repository\Chart')
                ->disableOriginalConstructor()
                ->getMock();

        $this->client->expects($this->once())
                ->method('getRepository')
                ->with('Chart')
                ->will($this->returnValue($repository));

        $factory = new EntityFactory($this->client);
        $result = $factory->createInstanceOf('Chart', $data);

        $this->assertInstanceOf('\ChartBlocks\Entity\Chart', $result);
        $this->assertEquals($data['name'], $result->name);
    }

    /**
     * @expectedException \ChartBlocks\Entity\Exception
     */
    public function testGivenErroneousData() {
        $factory = new EntityFactory($this->client);
        $factory->createInstanceOf('Chart', 'qwijibo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGivenErroneousClassName() {
        $factory = new EntityFactory($this->client);
        $result = $factory->createInstanceOf('Qwijibo', array('name' => 'Qwijibo'));
    }

}
