<?php

namespace OSS\Model;


use OSS\Core\OssException;

/**
 * Class ReservedCapacityConfig
 * @package OSS\Model
 *
 */
class CreateReservedCapacity implements XmlConfig
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $dataRedundancyType;
    /**
     * @var int|null
     */
    private $reservedCapacity;


    /**
     * CreateReservedCapacity constructor.
     * @param string $name
     * @param null|string $dataRedundancyType
     * @param null|int $reservedCapacity
     */
    public function __construct($name, $dataRedundancyType=null, $reservedCapacity=null) {
        $this->name = $name;
        $this->dataRedundancyType = $dataRedundancyType;
        $this->reservedCapacity = $reservedCapacity;
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
        if (isset($this->name)){
            $xml->addChild('Name', $this->name);
        }
        if (isset($this->dataRedundancyType)){
            $xml->addChild('DataRedundancyType', $this->dataRedundancyType);
        }
        if (isset($this->reservedCapacity)){
            $xml->addChild('ReservedCapacity', $this->reservedCapacity);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }
}