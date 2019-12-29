<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="Logs")
 */
class AccessLog
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $method;

    /**
     * @MongoDB\Field(type="string")
     */
    private $requestedResource;

    /**
     * @MongoDB\Field(type="integer")
     */
    private $responseStatus;

    /**
     * @MongoDB\Field(type="integer")
     */
    private $responseSize;

    /**
     * @MongoDB\Field(type="string")
     */
    private $referer;

    /**
     * @MongoDB\Field(type="string")
     */
    private $userAgent;

    public function getId(): ?int
    {
        return $this->id;
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
