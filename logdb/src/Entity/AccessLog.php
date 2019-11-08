<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccessLogRepository")
 * @ORM\Table(schema="public")
 */
class AccessLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Logger", inversedBy="accessLog", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $logger_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $method;

    /**
     * @ORM\Column(type="text")
     */
    private $requestedResource;

    /**
     * @ORM\Column(type="integer")
     */
    private $responseStatus;

    /**
     * @ORM\Column(type="integer")
     */
    private $responseSize;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $referer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userAgent;

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

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getRequestedResource(): ?string
    {
        return $this->requestedResource;
    }

    public function setRequestedResource(string $requestedResource): self
    {
        $this->requestedResource = $requestedResource;

        return $this;
    }

    public function getResponseStatus(): ?int
    {
        return $this->responseStatus;
    }

    public function setResponseStatus(int $responseStatus): self
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    public function getResponseSize(): ?int
    {
        return $this->responseSize;
    }

    public function setResponseSize(int $responseSize): self
    {
        $this->responseSize = $responseSize;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(string $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
