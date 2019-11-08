<?php

namespace App\Entity;

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
}
