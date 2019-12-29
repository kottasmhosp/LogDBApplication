<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="Logs")
 */
class Block
{

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private $block_number;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlockNumber(): ?string
    {
        return $this->block_number;
    }

    public function setBlockNumber(string $block_number): self
    {
        $this->block_number = $block_number;

        return $this;
    }

}
