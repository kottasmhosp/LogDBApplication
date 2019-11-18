<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\Table(schema="public")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\HdfsLog", mappedBy="blocks")
     */
    private $hdfsLogs;

    /**
     * @ORM\Column(type="bigint")
     */
    private $block_number;


    public function __construct()
    {
        $this->hdfsLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|HdfsLog[]
     */
    public function getHdfsLogs(): Collection
    {
        return $this->hdfsLogs;
    }

    public function addHdfsLog(HdfsLog $hdfsLog): self
    {
        if (!$this->hdfsLogs->contains($hdfsLog)) {
            $this->hdfsLogs[] = $hdfsLog;
            $hdfsLog->addBlock($this);
        }

        return $this;
    }

    public function removeHdfsLog(HdfsLog $hdfsLog): self
    {
        if ($this->hdfsLogs->contains($hdfsLog)) {
            $this->hdfsLogs->removeElement($hdfsLog);
            $hdfsLog->removeBlock($this);
        }

        return $this;
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
