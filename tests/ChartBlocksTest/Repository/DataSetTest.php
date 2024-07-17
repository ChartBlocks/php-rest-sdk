<?php

namespace ChartBlocksTest;

use ChartBlocks\Repository\DataSet;
use PHPUnit\Framework\TestCase;

class DataSetTest extends TestCase {

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

    public function testCreateFromFile() {
        $file = '/path/to/file';
        $uploadResult = array(
            'source' => 'csv',
            'file' => array(
                'id' => 1
            )
        );
        $importData = array(
            'sourceName' => $uploadResult['source'],
            'sourceOptions' => array(
                'fileId' => $uploadResult['file']['id']
            ),
            'testOptions' => array(
                'testPassingThroughArbitaryOptions'
            )
        );
        $importResult = array(
            'id' => '2'
        );

        $client = $this->createMock('\ChartBlocks\Client');
        $dataSet = $this->createMock('\ChartBlocks\Repository\DataSet');

        $dataSet->expects($this->any())
                ->method('getClient')
                ->will($this->returnValue($client));

        $client->expects($this->once())
                ->method('postFile')
                ->with('upload', $file)
                ->will($this->returnValue($uploadResult));

        $client->expects($this->once())
                ->method('post')
                ->with('set/import', $importData)
                ->will($this->returnValue($importResult));

        $dataSet->expects($this->once())
                ->method('findById')
                ->with($importResult['id']);

        $dataSet->createFromFile($file, array('testOptions' => $importData['testOptions']));
    }

}
