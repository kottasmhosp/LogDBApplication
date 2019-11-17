<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\Table(schema="public",indexes={@Index(name="search_idx", columns={"block_name"})})
 */
class Block
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\HdfsLog", inversedBy="blocks")
     */
    private $block_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $block_name;

    public function __construct()
    {
        $this->block_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|HdfsLog[]
     */
    public function getBlockId(): Collection
    {
        return $this->block_id;
    }

    public function addBlockId(HdfsLog $blockId): self
    {
        if (!$this->block_id->contains($blockId)) {
            $this->block_id[] = $blockId;
        }

        return $this;
    }

    public function removeBlockId(HdfsLog $blockId): self
    {
        if ($this->block_id->contains($blockId)) {
            $this->block_id->removeElement($blockId);
        }

        return $this;
    }

    public function getBlockName(): ?int
    {
        return $this->block_name;
    }

    public function setBlockName(int $block_name): self
    {
        $this->block_name = $block_name;

        return $this;
    }
}
