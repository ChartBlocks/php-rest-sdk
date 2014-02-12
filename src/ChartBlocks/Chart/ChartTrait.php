<?php

namespace ChartBlocks\Chart;

use ChartBlocks\Chart;

trait ChartTrait {

    protected $chart;

    public function getChart() {
        return $this->chart;
    }

    public function setChart(Chart $chart) {
        $this->chart = $chart;
        return $this;
    }

}