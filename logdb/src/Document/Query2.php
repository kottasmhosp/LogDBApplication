<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;

/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query2 {
    /** @MongoDB\Field(type="collection") */
    private $_id;

    /** @MongoDB\Field(type="int") */
    private $type;

    /** @MongoDB\Field(type="int") */
    private $count;

    /**
     * @return array
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return integer
     */
    public function getType(){
        return $this->type;
    }

    /**
     * @return integer
     */
    public function getCount(){
        return $this->count;
    }

}