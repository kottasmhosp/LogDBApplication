<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;


/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query6
{
    /** @MongoDB\Field(type="collection") */
    private $_id;

    /** @MongoDB\Field(type="integer") */
    private $distinctTypes;

    /** @MongoDB\Field(type="integer") */
    private $distinctValues;

    /**
     * @return array
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return integer
     */
    public function getDistinctTypes(){
        return $this->distinctTypes;
    }

    /**
     * @return integer
     */
    public function getDistinctValues(){
        return $this->distinctValues;
    }

}