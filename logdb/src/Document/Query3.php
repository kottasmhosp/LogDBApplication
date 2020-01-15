<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;
/**
 * @MongoDB\QueryResultDocument
 *
 */
class Query3
{

    /** @MongoDB\Field(type="collection") */
    private $_id;

    /** @MongoDB\Field(type="collection") */
    private $total_types_per_sourceIp;

    /**
     * @return string
     */
    public function getId(){
        return $this->_id;
    }

    /**
     * @return array
     */
    public function getTotalTypesPerSourceIp(){
        return $this->total_types_per_sourceIp;
    }

}