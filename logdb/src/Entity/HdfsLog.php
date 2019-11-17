<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HdfsLogRepository")
 * @ORM\Table(schema="public")
 */
class HdfsLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Logger", inversedBy="hdfsLog", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $logger_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $blockId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Block", mappedBy="block_id")
     */
    private $blocks;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoggerId(): ?Logger
    {
        return $this->logger_id;
    }

    public function setLoggerId(Logger $logger_id): self
    {
        $this->logger_id = $logger_id;

        return $this;
    }

    public function getBlockId(): ?string
    {
        return $this->blockId;
    }

    public function setBlockId(string $blockId): self
    {
        $this->blockId = $blockId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->addBlockId($this);
        }

        return $this;
    }

    public function removeBlock(Block $block): self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            $block->removeBlockId($this);
        }

        return $this;
    }
}
