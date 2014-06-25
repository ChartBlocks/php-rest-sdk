<?php

namespace ChartBlocks\Chart;

use ChartBlocks\Entity\Chart;

interface ChartAwareInterface {

    public function getChart();

    public function setChart(Chart $chart);
}