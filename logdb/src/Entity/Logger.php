<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LoggerRepository")
 * @ORM\Table(schema="public")
 */
class Logger
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourceIp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destIp;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeStamp;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\AccessLog", mappedBy="logger_id", cascade={"persist", "remove"})
     */
    private $accessLog;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\HdfsLog", mappedBy="logger_id", cascade={"persist", "remove"})
     */
    private $hdfsLog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceIp(): ?string
    {
        return $this->sourceIp;
    }

    public function setSourceIp(string $sourceIp): self
    {
        $this->sourceIp = $sourceIp;

        return $this;
    }

    public function getDestIp(): ?string
    {
        return $this->destIp;
    }

    public function setDestIp(string $destIp): self
    {
        $this->destIp = $destIp;

        return $this;
    }

    public function getTimeStamp(): ?int
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(int $timeStamp): self
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    public function getAccessLog(): ?AccessLog
    {
        return $this->accessLog;
    }

    public function setAccessLog(AccessLog $accessLog): self
    {
        $this->accessLog = $accessLog;

        // set the owning side of the relation if necessary
        if ($this !== $accessLog->getLoggerId()) {
            $accessLog->setLoggerId($this);
        }

        return $this;
    }

    public function getHdfsLog(): ?HdfsLog
    {
        return $this->hdfsLog;
    }

    public function setHdfsLog(HdfsLog $hdfsLog): self
    {
        $this->hdfsLog = $hdfsLog;

        // set the owning side of the relation if necessary
        if ($this !== $hdfsLog->getLoggerId()) {
            $hdfsLog->setLoggerId($this);
        }

        return $this;
    }
}
