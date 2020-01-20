<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;


/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query7
{
    /** @MongoDB\Field(type="string") */
    private $_id;

    /** @MongoDB\Field(type="integer") */
    private $totalVotes;

    /**
     * @return string
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return integer
     */
    public function getTotalVotes(){
        return $this->totalVotes;
    }

}