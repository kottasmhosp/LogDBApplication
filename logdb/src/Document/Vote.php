<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @MongoDB\Document(collection="Votes")
 */
class Vote
{

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="collection") @MongoDB\Index
     */
    private $admins;

    /**
     * @MongoDB\Field(type="string") @MongoDB\Index
     */
    private $log_id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $source_ip;

    public function __construct()
    {
        $this->admins = array();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAdmins(): array
    {
        return $this->admins;
    }

    public function addAdmin(string $admins): self
    {
        if (!in_array($admins, $this->admins)) {
            $this->admins[] = $admins;
        }

        return $this;
    }

    public function getLogId(): ?string
    {
        return $this->log_id;
    }

    public function setLogId(string $log_id): self
    {
        $this->log_id = $log_id;

        return $this;
    }

    public function getSourceIp(): ?string
    {
        return $this->source_ip;
    }

    public function setSourceIp(string $source_ip): self
    {
        $this->source_ip = $source_ip;

        return $this;
    }


}
