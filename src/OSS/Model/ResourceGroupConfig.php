<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class ResourceGroupConfig
 * @package OSS\Model
 */
class ResourceGroupConfig implements XmlConfig
{
    /**
     * Parse ResourceGroupConfig from the xml.
     * @param string $strXml
     * @return null
     * @throws OssException
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (isset($xml->ResourceGroupId)) {
            $this->resourceGroupId = strval($xml->ResourceGroupId);
        }
    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><BucketResourceGroupConfiguration></BucketResourceGroupConfiguration>');
        if (isset($this->resourceGroupId)) {
            $xml->addChild('ResourceGroupId', $this->resourceGroupId);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }


    /**
     * @return string
     */
    public function getResourceGroupId()
    {
        return $this->resourceGroupId;
    }

    /**
     * @param string $resourceGroupId
     */
    public function setResourceGroupId($resourceGroupId)
    {
        $this->resourceGroupId = $resourceGroupId;
    }

    /**
     * @var string
     */
    private $resourceGroupId;
}