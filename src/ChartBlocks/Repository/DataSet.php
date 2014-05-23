<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Entity\DataSet as EntityDataSet;
use ChartBlocks\DataSet\Creator;

class DataSet extends AbstractRepository {

    protected $url = '/set';
    protected $class = '\\ChartBlocks\\Entity\\DataSet';
    protected $singleResponseKey = 'set';
    protected $listResponseKey = 'sets';

    public function create(array $options = array()) {
        $client = $this->getHttpClient();

        $params = array(
            'sourceName' => 'blank'
        );

        if ($name = (isset($options['name']) ? $options['name'] : null)) {
            unset($options['name']);
            $params['name'] = $name;
        } else {
            $params['name'] = 'Untitled set';
        }


        if ($file = (isset($options['file']) ? $options['file'] : null)) {

            unset($options['file']);

            $fileJson = $client->postFile('upload', $file);

            $params['sourceOptions'] = array_merge($options, array(
                'fileId' => $fileJson['file']['id'],
            ));
            $params['sourceName'] = $fileJson['source'];
        }
        
        
        $importData = $client->postJson('set/import', $params);

        return $this->findById($importData['id']);
    }

    /**
     * TRUNCATE SET, THERE IS NO GOING BACK
     * @param ChartBlocks\Entity\DataSet|string $set
     */
    public function truncate($set) {
        $id = $this->extractId($set);
        if ($json = $this->getHttpClient()->deleteJson('data/' . $id)) {
            return !!$json['ok'];
        }
        return false;
    }

    /**
     * DELETE SET, THERE IS NO GOING BACK
     * @param ChartBlocks\Entity\DataSet|string $set
     */
    public function delete($set) {
        $id = $this->extractId($set);
        if ($json = $this->getHttpClient()->deleteJson('set/' . $id)) {
            return !!$json['result'];
        }
        return false;
    }

    /**
     * @param \ChartBlocks\Entity\DataSet|string $set
     * @return string $setId
     */
    private function extractId($set) {
        if ($set instanceof EntityDataSet) {
            return $set->getId();
        } else {
            return $set;
        }
    }

}
