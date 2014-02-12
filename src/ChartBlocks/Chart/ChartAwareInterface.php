<?php

namespace ChartBlocks\Chart;

use ChartBlocks\Chart;

interface ChartAwareInterface {

    public function getChart();

    public function setChart(Chart $chart);
}