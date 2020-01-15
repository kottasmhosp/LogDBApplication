<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;


/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query5
{
    /** @MongoDB\Field(type="collection") */
    private $_id;

    /** @MongoDB\Field(type="integer") */
    private $totalCount;

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
    public function getTotalCount(){
        return $this->totalCount;
    }

    /**
     * @return integer
     */
    public function getDistinctValues(){
        return $this->distinctValues;
    }

}