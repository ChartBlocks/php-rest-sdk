<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Chart\Config;

class Chart extends AbstractEntity {

    protected $id;
    protected $config;
    protected $creator;
    protected $images;
    protected $totalViews;
    protected $createdAt;
    protected $updatedAt;
    protected $publicUrl;

    public function setData($data) {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }

        if (array_key_exists('name', $data)) {
            $this->setName($data['name']);
        }

        if (array_key_exists('images', $data)) {
            $this->setImages($data['images']);
        }

        if (array_key_exists('config', $data)) {
            $this->setConfig($data['config']);
        }

        if (array_key_exists('totalViews', $data)) {
            $this->setTotalViews($data['totalViews']);
        }

        if (array_key_exists('publicUrl', $data)) {
            $this->setPublicUrl($data['publicUrl']);
        }

        if (array_key_exists('createdAt', $data)) {
            $this->setCreatedAt($data['createdAt']);
        }

        if (array_key_exists('creator', $data)) {
            $this->setCreator(new Profile($this->getRepository(), $data['creator']));
        }

        return parent::setData($data);
    }

    public function setPublicUrl($publicUrl) {
        $this->publicUrl = $publicUrl;
        return $this;
    }

    public function getPublicUrl() {
        return $this->publicUrl;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setTotalViews($views) {
        $this->totalViews = $views;
        return $this;
    }

    public function getTotalViews() {
        return $this->totalViews;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setCreator(Profile $creator) {
        $this->creator = $creator;
        return $this;
    }

    public function getCreator() {
        return $this->creator;
    }

    public function getImages() {
        return $this->images;
    }

    public function setImages(array $images) {
        $this->images = $images;
        return $this;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setConfig($config) {
        if ($config instanceof Config) {
            $this->config = $config;
        } else if (is_array($config)) {
            $this->config = new Config($config);
        } else {
            throw new Exception('Config given is not an instance of \ChartBlocks\Chart\Config or an array');
        }
        return $this;
    }

    public function getConfig() {
        return $this->config;
    }

}
