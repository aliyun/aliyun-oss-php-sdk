<?php

namespace OSS\Model;


use OSS\Core\OssException;

/**
 * Class AccessPointConfig
 * @package OSS\Model
 *
 */
class AccessPointConfig implements XmlConfig
{
    const VPC = 'vpc';
    const INTERNET = 'internet';
    private $accessPointName;
    private $networkOrigin;
    private $vpcId;

    /**
     * AccessPointConfig constructor.
     * @param string|null $accessPointName
     * @param string|null $networkOrigin
     * @param string|null $vpcId
     */
    public function __construct($accessPointName=null,$networkOrigin=null,$vpcId=null)
    {
        $this->accessPointName = $accessPointName;
        $this->networkOrigin = $networkOrigin;
        $this->vpcId = $vpcId;
    }


    public function setAccessPointName($accessPointName)
    {
        $this->accessPointName = $accessPointName;
    }

    public function setNetworkOrigin($networkOrigin)
    {
        $this->networkOrigin = $networkOrigin;
    }

    public function setVpcId($vpcId)
    {
        $this->vpcId = $vpcId;
    }

    /**
     * Parse TaggingConfig from the xml.
     *
     * @param string $strXml
     * @return null
     */
    public function parseFromXml($strXml)
    {
    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><CreateAccessPointConfiguration></CreateAccessPointConfiguration>');
        if (isset($this->accessPointName)){
            $xml->addChild('AccessPointName',strval($this->accessPointName));
        }
        if (isset($this->networkOrigin)){
            $xml->addChild('NetworkOrigin',strval($this->networkOrigin));
        }
        if (isset($this->vpcId)){
            $xmlVpc = $xml->addChild('VpcConfiguration');
            $xmlVpc->addChild('VpcId',$this->vpcId);
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