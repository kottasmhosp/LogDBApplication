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
    private $admin_ids;

    /**
     * @MongoDB\Field(type="string") @MongoDB\Index
     */
    private $log_id;

    public function __construct()
    {
        $this->admin_ids = array();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAdminId(): array
    {
        return $this->admin_ids;
    }

    public function addAdminId(string $admin_id): self
    {
        if (!in_array($admin_id, $this->admin_ids)) {
            $this->admin_ids[] = $admin_id;
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


}
