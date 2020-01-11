<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @MongoDB\Document(collection="Votes")
 * @MongoDBUnique(fields="username")
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
    private $admin_id;

    /**
     * @MongoDB\Field(type="string") @MongoDB\Index
     */
    private $log_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAdminId(): array
    {
        return $this->admin_id;
    }

    public function addAdminId(string $admin_id): self
    {
        if (!in_array($admin_id, $this->admin_id)) {
            $this->admin_id[] = $admin_id;
        }

        return $this;
    }

    public function getLogId(): ?string
    {
        return $this->log_id;
    }

    public function setLogId(string $admin_id): self
    {
        $this->admin_id = $admin_id;

        return $this;
    }


}
