<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;


/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query10
{
    /** @MongoDB\Field(type="collection") */
    private $_id;

    /** @MongoDB\Field(type="collection") */
    private $logs;

    /** @MongoDB\Field(type="integer") */
    private $totalEmails;

    /**
     * @return array
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return array
     */
    public function getLogs(){
        return $this->logs;
    }

    /**
     * @return integer
     */
    public function getTotalEmails(){
        return $this->totalEmails;
    }

}