<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\Collection;

/**
 * @MongoDB\Document(collection="Logs")
 *
 */
class Log
{
    /**
     * @MongoDB\Id
     */
    private $id;

    //Common
    /**
     * @MongoDB\Field(type="string")
     */
    private $sourceIp;

    /**
     * @MongoDB\Field(type="string")
     */
    private $destIp;

    /**
     * @MongoDB\Field(type="timestamp")
     */
    private $insertDate;

    //HDFS
    /**
     * @MongoDB\Field(type="string")
     */
    private $type;

    /**
     * @MongoDB\Field(type="int")
     */
    private $size;

    //Access Log Fields
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

    //Blocks
    /**
     * @MongoDB\Field(type="collection") @MongoDB\Index
     */
    private $blocks;

    public function __construct()
    {
        $this->blocks = array();
    }

    //General fields Setters-Getters
    public function getId(): ?string
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

    public function getInsertDate(): ?int
    {
        return $this->insertDate;
    }

    public function setInsertDate(int $insertDate): self
    {
        $this->insertDate = $insertDate;

        return $this;
    }


    //ACCESS Setters-Getters
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

    //HDFS Setters-Getters
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

    //Block Setter-Getter

    /**
     * @return array
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function addBlock(int $block): self
    {
        if (!in_array($block, $this->blocks)) {
            $this->blocks[] = $block;
        }

        return $this;
    }

    public function setBlock(array $block): self
    {
        $this->blocks = $block;

        return $this;
    }

    public function removeBlock(int $block): self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
        }

        return $this;
    }

    public function setBlockNull(): self
    {
        $this->blocks = NULL;

        return $this;
    }


}
