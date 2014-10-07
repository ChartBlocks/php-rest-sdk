<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Entity\DataSet as EntityDataSet;
use ChartBlocks\DataSet\Creator;

class DataSet extends AbstractWriteableRepository {

    public $url = 'set/';
    public $class = '\\ChartBlocks\\Entity\\DataSet';
    public $singleResponseKey = 'set';
    public $listResponseKey = 'sets';

    public function createFromFile($file, array $data = array()) {
        $uploadResult = $this->getClient()->postFile('upload', $file);

        $importData = array_merge($data, array(
            'sourceName' => $uploadResult['source'],
            'sourceOptions' => array(
                'fileId' => $uploadResult['file']['id']
            )
        ));

        $importResult = $this->getClient()->post('set/import', $importData);
        return $this->findById($importResult['id']);
    }

}
