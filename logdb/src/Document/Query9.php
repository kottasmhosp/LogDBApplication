<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;


/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query9
{
    /** @MongoDB\Field(type="collection") */
    private $_id;

    /** @MongoDB\Field(type="integer") */
    private $totalVotesPerSourceIp;

    /**
     * @return array
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return integer
     */
    public function getTotalVotesPerSourceIp(){
        return $this->totalVotesPerSourceIp;
    }

}