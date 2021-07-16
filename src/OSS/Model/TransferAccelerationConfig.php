<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class TransferAccelerationConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/214603.html
 */
class TransferAccelerationConfig implements XmlConfig
{

    /**
     * Parse TransferAccelerationConfig from the xml.
     * @param string $strXml
     * @throws OssException
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Enabled)) return;
        $this->enabled = strval($xml->Enabled);
    }

    /**
     * Serialize the object into xml string.
     *sss
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><TransferAccelerationConfiguration></TransferAccelerationConfiguration>');
        if (isset($this->enabled)) {
            $xml->addChild('Enabled',$this->enabled);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }

    /**
     * set enabled
     * @param $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = strval($enabled);
    }


    /**
     * @return string 'true' 'false'
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @var $enabled string 'true' 'false'
     */
    private $enabled;
}


