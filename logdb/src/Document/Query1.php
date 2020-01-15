<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;

/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query1 {
    /** @MongoDB\Field(type="string") */
    private $_id;

    /** @MongoDB\Field(type="int") */
    private $count;

    /**
     * @return string
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return integer
     */
    public function getCount(){
        return $this->count;
    }

}