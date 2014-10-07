<?php

namespace ChartBlocksTest;

use ChartBlocks\Repository\AbstractWriteableRepository;

class AbstractWriteableRepositoryTest extends \PHPUnit_Framework_TestCase {

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
        $this->repo = $this->getMockForAbstractClass('\ChartBlocks\Repository\AbstractWriteableRepository', array($this->client));

        $this->repo->url = 'chart';
        $this->repo->class = '\ChartBlocks\Entity\Chart';
        $this->repo->singleResponseKey = 'chart';
        $this->repo->listResponseKey = 'charts';
    }

    public function testCreate() {
        $data = array('name' => 'My chart');
        $response = array('chart' => array('name' => 'My chart'));

        $this->client->expects($this->once())
                ->method('post')
                ->with($this->repo->url, $data)
                ->will($this->returnValue($response));

        $repo = $this->getMock('\ChartBlocks\Repository\AbstractWriteableRepository', array(/* 'extractSingleItemData', */'igniteEntity'), array($this->client));
        $repo->url = 'chart';
        $repo->singleResponseKey = 'chart';
//        $repo->expects($this->once())
//                ->method('extractSingleItemData')
//                ->with($response)
//                ->will($this->returnValue($response['chart']));

        $repo->expects($this->once())
                ->method('igniteEntity')
                ->with($response['chart'])
                ->will($this->returnValue('ENTITY'));

        $result = $repo->create($data);
        $this->assertEquals('ENTITY', $result);
    }

    /**
     * @expectedException \ChartBlocks\Repository\Exception
     */
    public function testUpdateWithEntityLackingId() {
        $entity = $this->getMockForAbstractClass('\ChartBlocks\Entity\AbstractEntity', array($this->repo));
        $this->repo->update($entity);
    }

    public function testUpdate() {
        $id = '541fdd38c9a61d68707f9d86';
        $data = array('name' => 'My Entity');
        $entity = $this->getMock('\ChartBlocks\Entity\AbstractEntity', array('getId', 'toArray'), array($this->repo));
        $entity->expects($this->once())
                ->method('getId')
                ->will($this->returnValue($id));
        $entity->expects($this->once())
                ->method('toArray')
                ->will($this->returnValue($data));

        $this->client->expects($this->once())
                ->method('put')
                ->with($this->repo->url . '/' . $id, $data);

        $result = $this->repo->update($entity);
        $this->assertSame($this->repo, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteWithInvalidId() {
        $this->repo->delete('qwijibo');
    }

    public function testDeleteWithId() {
        $id = '541fdd38c9a61d68707f9d86';

        $this->client->expects($this->once())
                ->method('delete')
                ->with($this->repo->url . '/' . $id)
                ->will($this->returnValue(array('result' => true)));

        $result = $this->repo->delete($id);
        $this->assertTrue($result);
    }

    public function testDeleteWithEntity() {
        $id = '541fdd38c9a61d68707f9d86';
        $entity = $this->getMock('\ChartBlocks\Entity\AbstractEntity', array('getId'), array($this->repo));
        $entity->expects($this->once())
                ->method('getId')
                ->will($this->returnValue($id));

        $this->client->expects($this->once())
                ->method('delete')
                ->with($this->repo->url . '/' . $id)
                ->will($this->returnValue(array('result' => true)));

        $result = $this->repo->delete($entity);
        $this->assertTrue($result);
    }

    /**
     * @depends testDeleteWithId
     */
    public function testDeleteWithInvalidResponse() {
        $id = '541fdd38c9a61d68707f9d86';

        $this->client->expects($this->once())
                ->method('delete')
                ->with($this->repo->url . '/' . $id)
                ->will($this->returnValue(array('notRight' => true)));

        $result = $this->repo->delete($id);
        $this->assertFalse($result);
    }

}
