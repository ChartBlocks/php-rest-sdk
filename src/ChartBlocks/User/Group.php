<?php

namespace ChartBlocks\User;

class Group {

    /**
     *
     * @var string 
     */
    protected $name;

    /**
     *
     * @var string 
     */
    protected $id;

    /**
     *
     * @var string 
     */
    protected $description;

    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = array()) {
        $this->setConfig($data);
    }

    /**
     * 
     * @param array $config
     * @return \ChartBlocks\User\Group
     */
    public function setConfig(array $config = array()) {

        if (array_key_exists('name', $config)) {
            $this->setName($config['name']);
        }
        if (array_key_exists('id', $config)) {
            $this->setId($config['id']);
        }
        if (array_key_exists('description', $config)) {
            $this->setDescription($config['description']);
        }
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return \ChartBlocks\User\Group
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * 
     * @param string $id
     * @return \ChartBlocks\User\Group
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * 
     * @param type $description
     * @return \ChartBlocks\User\Group
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

}
