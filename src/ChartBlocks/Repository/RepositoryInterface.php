<?php

namespace ChartBlocks\Repository;

interface RepositoryInterface {

    public function find(array $query);

    public function findById($id);

    public function getClient();
}
