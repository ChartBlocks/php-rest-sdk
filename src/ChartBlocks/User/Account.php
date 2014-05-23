<?php

namespace ChartBlocks\User;

class Account {

    protected $id;
    protected $name;
    protected $plan;
    protected $chosenPlan;
    protected $active;
    protected $cardActive;
    protected $demo;
    protected $chartLimit;
    protected $setLimit;
    protected $viewLimit;
    protected $userLimit;
    protected $countryCode;
    protected $company;
    protected $vatNumber;
    protected $stripeCustomerId;
    protected $editable;

    public function __construct(array $data = array()) {
        $this->setConfig($data);
    }

    public function setConfig(array $config = array()) {

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPlan() {
        return $this->plan;
    }

    public function getChosenPlan() {
        return $this->chosenPlan;
    }

    public function getActive() {
        return $this->active;
    }

    public function getCardActive() {
        return $this->cardActive;
    }

    public function getDemo() {
        return $this->demo;
    }

    public function getChartLimit() {
        return $this->chartLimit;
    }

    public function getSetLimit() {
        return $this->setLimit;
    }

    public function getViewLimit() {
        return $this->viewLimit;
    }

    public function getUserLimit() {
        return $this->userLimit;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function getCompany() {
        return $this->company;
    }

    public function getVatNumber() {
        return $this->vatNumber;
    }

    public function getStripeCustomerId() {
        return $this->stripeCustomerId;
    }

    public function getEditable() {
        return $this->editable;
    }

}
