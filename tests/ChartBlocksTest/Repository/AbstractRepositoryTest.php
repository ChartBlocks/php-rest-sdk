<?php

namespace ChartBlocksTest;

use ChartBlocks\Repository\AbstractRepository;

class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \ChartBlocks\Repository\AbstractRepository;
     */
    protected $repo;

    /**
     *
     * @var \ChartBlocks\Client;
     */
    protected $client;

    public function setUp() {
        $this->client = $this->getMock('\ChartBlocks\Client');
        $this->repo = $this->getMockForAbstractClass('\ChartBlocks\Repository\AbstractRepository', array($this->client));

        $this->repo->url = 'chart';
        $this->repo->class = '\ChartBlocks\Entity\Chart';
        $this->repo->singleResponseKey = 'chart';
        $this->repo->listResponseKey = 'charts';
    }

    public function testClientSetOnConstruction() {
        $this->assertInstanceOf('\ChartBlocks\Client', $this->repo->getClient());
    }

    public function testFind() {
        $query = array('public' => 1);
        $response = array(
            'charts' => array(
                array('name' => 'My chart 1'),
                array('name' => 'My chart 2'),
            ),
            'state' => array(
                'totalRecords' => 10
            )
        );

        $this->client->expects($this->once())
                ->method('get')
                ->with($this->repo->url, $query)
                ->will($this->returnValue($response));

        $repo = $this->getMock('\ChartBlocks\Repository\AbstractRepository', array(/* 'extractListItemData', */'igniteEntity'), array($this->client));
        $repo->url = 'chart';
        $repo->listResponseKey = 'charts';
//        $repo->expects($this->once())
//                ->method('extractListItemData')
//                ->with($response)
//                ->will($this->returnValue($response['charts']));

        $repo->expects($this->exactly(2))
                ->method('igniteEntity')
                ->withConsecutive(array($response['charts'][0]), array($response['charts'][1]))
                ->will($this->returnValue('ENTITY'));

        $result = $repo->find($query);

        $this->assertCount(2, $result);
        $this->assertEquals(10, $result->getTotalRecords());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindByIdWithInvalidId() {
        $this->repo->findById('qwijibo');
    }

    public function testFindById() {
        $id = '541fdd38c9a61d68707f9d86';
        $query = array('t' => '4ee3b408');
        $response = array('chart' => array('name' => 'My chart'));

        $this->client->expects($this->once())
                ->method('get')
                ->with($this->repo->url . '/' . $id, $query)
                ->will($this->returnValue($response));

        $repo = $this->getMock('\ChartBlocks\Repository\AbstractRepository', array(/* 'extractSingleItemData', */'igniteEntity'), array($this->client));
        $repo->url = 'chart';
        $repo->singleResponseKey = 'chart';

        $repo->expects($this->once())
                ->method('igniteEntity')
                ->with($response['chart'])
                ->will($this->returnValue('ENTITY'));

        $result = $repo->findById($id, $query);
        $this->assertEquals('ENTITY', $result);
    }

    /**
     * @expectedException \ChartBlocks\Repository\Exception
     */
    public function testExceptionThrownWhenMissingSingleResponseKey() {
        $id = '541fdd38c9a61d68707f9d86';
        $response = array('qwijibo' => null);

        $this->client->expects($this->once())
                ->method('get')
                ->with($this->repo->url . '/' . $id)
                ->will($this->returnValue($response));

        $repo = $this->getMock('\ChartBlocks\Repository\AbstractRepository', array(/* 'extractSingleItemData', */'igniteEntity'), array($this->client));
        $repo->url = 'chart';
        $repo->singleResponseKey = 'chart';

        $result = $repo->findById($id);
    }

    /**
     * @expectedException \ChartBlocks\Repository\Exception
     */
    public function testExceptionThrownWhenMissingListResponseKey() {
        $response = array('qwijibo' => null);

        $this->client->expects($this->once())
                ->method('get')
                ->with($this->repo->url)
                ->will($this->returnValue($response));

        $repo = $this->getMock('\ChartBlocks\Repository\AbstractRepository', array(/* 'extractSingleItemData', */'igniteEntity'), array($this->client));
        $repo->url = 'chart';
        $repo->listResponseKey = 'charts';

        $result = $repo->find();
    }

    /**
     * @expectedException \ChartBlocks\Repository\Exception
     */
    public function testIgniteEntityWithEmptyClass() {
        $this->repo->class = null;
        $this->repo->igniteEntity(array('name' => 'My chart'));
    }

    /**
     * @expectedException \ChartBlocks\Repository\Exception
     */
    public function testIgniteEntityWithMissingClass() {
        $this->repo->class = '\ChartBlocks\Entity\Qwijibo';
        $this->repo->igniteEntity(array('name' => 'My chart'));
    }

    public function testIgniteEntity() {
        $this->repo->class = '\ChartBlocks\Entity\Chart';
        $result = $this->repo->igniteEntity(array('name' => 'My chart'));

        $this->assertInstanceOf($this->repo->class, $result);
        $this->assertEquals('My chart', $result->name);
    }

}
