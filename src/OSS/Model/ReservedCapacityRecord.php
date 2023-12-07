<?php

namespace OSS\Model;

/**
 * Class ReservedCapacityRecord
 * @package OSS\Model
 */
class ReservedCapacityRecord
{

    /**
     * @var string
     */
    private $instanceId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $ownerId;
    /**
     * @var string
     */
    private $ownerDisplayName;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $status;
    /**
     * @var string
     */
    private $dataRedundancyType;
    /**
     * @var int
     */
    private $reservedCapacity;
    /**
     * @var int
     */
    private $autoExpansionSize;
    /**
     * @var int
     */
    private $autoExpansionMaxSize;
    /**
     * @var int
     */
    private $createTime;
    /**
     * @var int
     */
    private $lastModifyTime;
    /**
     * @var int
     */
    private $enableTime;

    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        $this->parseFromXmlObj($xml);
    }


    /**
     * @param $xml \SimpleXMLElement
     */
    public function parseFromXmlObj($xml)
    {
        if (isset($xml->InstanceId)){
            $this->instanceId = strval($xml->InstanceId);
        }
        if (isset($xml->Name)){
            $this->name = strval($xml->Name);
        }
        if (isset($xml->Owner)){
            $this->parseOwner($xml->Owner);
        }
        if (isset($xml->Region)){
            $this->region = strval($xml->Region);
        }
        if (isset($xml->Status)){
            $this->status = strval($xml->Status);
        }
        if (isset($xml->DataRedundancyType)){
            $this->dataRedundancyType = strval($xml->DataRedundancyType);
        }
        if (isset($xml->ReservedCapacity)){
            $this->reservedCapacity = intval($xml->ReservedCapacity);
        }
        if (isset($xml->AutoExpansionSize)){
            $this->autoExpansionSize = intval($xml->AutoExpansionSize);
        }
        if (isset($xml->AutoExpansionMaxSize)){
            $this->autoExpansionMaxSize = intval($xml->AutoExpansionMaxSize);
        }
        if (isset($xml->CreateTime)){
            $this->createTime = intval($xml->CreateTime);
        }
        if (isset($xml->LastModifyTime)){
            $this->lastModifyTime = intval($xml->LastModifyTime);
        }
        if (isset($xml->EnableTime)){
            $this->enableTime = intval($xml->EnableTime);
        }
    }

    /**
     * @param $xmlOwner \SimpleXMLElement
     */
    public function parseOwner($xmlOwner)
    {
        if (isset($xmlOwner->ID)){
            $this->ownerId = strval($xmlOwner->ID);
        }
        if (isset($xmlOwner->DisplayName)){
            $this->ownerDisplayName = strval($xmlOwner->DisplayName);
        }
    }

    /**
     * @return string
     */
    public function getInstanceId() {
        return $this->instanceId;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOwnerId() {
        return $this->ownerId;
    }

    /**
     * @return string
     */
    public function getOwnerDisplayName() {
        return $this->ownerDisplayName;
    }

    /**
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDataRedundancyType() {
        return $this->dataRedundancyType;
    }

    /**
     * @return int
     */
    public function getReservedCapacity() {
        return $this->reservedCapacity;
    }

    /**
     * @return int
     */
    public function getAutoExpansionSize() {
        return $this->autoExpansionSize;
    }

    /**
     * @return int
     */
    public function getAutoExpansionMaxSize() {
        return $this->autoExpansionMaxSize;
    }

    /**
     * @return int
     */
    public function getCreateTime() {
        return $this->createTime;
    }

    /**
     * @return int
     */
    public function getLastModifyTime() {
        return $this->lastModifyTime;
    }

    /**
     * @return int
     */
    public function getEnableTime() {
        return $this->enableTime;
    }


}