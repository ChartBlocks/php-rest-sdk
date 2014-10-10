<?php

namespace ChartBlocks\Entity;

trait DateModifiedTrait {

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

}
