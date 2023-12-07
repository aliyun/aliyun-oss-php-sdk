<?php

namespace OSS\Model;


use OSS\Core\OssException;

/**
 * Class ReservedCapacityConfig
 * @package OSS\Model
 *
 */
class UpdateReservedCapacity implements XmlConfig
{
    /**
     * @var string|null
     */
    private $status;
    /**
     * @var int|null
     */
    private $reservedCapacity;
    /**
     * @var int|null
     */
    private $autoExpansionSize;
    /**
     * @var int|null
     */
    private $autoExpansionMaxSize;

    /**
     * UpdateReservedCapacity constructor.
     * @param null|string $status
     * @param null|int $reservedCapacity
     * @param null|int $autoExpansionSize
     * @param null|int $autoExpansionMaxSize
     */
    public function __construct($status=null,$reservedCapacity=null,$autoExpansionSize=null,$autoExpansionMaxSize=null) {
        $this->status = $status;
        $this->reservedCapacity = $reservedCapacity;
        $this->autoExpansionSize = $autoExpansionSize;
        $this->autoExpansionMaxSize = $autoExpansionMaxSize;
    }

    /**
     * Parse ExtendWormConfig from the xml.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
     */
    public function parseFromXml($strXml)
    {
        throw new OssException("Not implemented.");
    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ReservedCapacityConfiguration></ReservedCapacityConfiguration>');
        if (isset($this->status)){
            $xml->addChild('Status', $this->status);
        }
        if (isset($this->reservedCapacity)){
            $xml->addChild('ReservedCapacity', $this->reservedCapacity);
        }
        if (isset($this->autoExpansionSize)){
            $xml->addChild('AutoExpansionSize', $this->autoExpansionSize);
        }
        if (isset($this->autoExpansionMaxSize)){
            $xml->addChild('AutoExpansionMaxSize', $this->autoExpansionMaxSize);
        }
        return $xml->asXML();
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->serializeToXml();
    }
}