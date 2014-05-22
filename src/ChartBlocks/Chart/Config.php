<?php

namespace ChartBlocks\Chart;

class Config implements ChartAwareInterface {

    protected $chart;
    protected $data;

    public function __construct($data = array()) {
        $this->data = $data;
    }

    public function __get($name) {
        if (isset($this->data[$name])) {

            if ($this->data[$name] instanceof self) {

                return $this->data[$name];
            } else if (is_array($this->data[$name])) {

                return $this->data[$name] = new self($this->data[$name]);
            }
            return $this->data[$name];
        }
    }

    public function getChart() {
        return $this->chart;
    }

    public function setChart(Chart $chart) {
        $this->chart = $chart;
        return $this;
    }

}
