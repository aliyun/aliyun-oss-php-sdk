<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class ReplicationLocation
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/181410.html
 */
class ReplicationLocation implements XmlConfig
{

    private $replicationLocation;


    /**
     * @param $location string
     */
    public function setLocations($location){
        $this->replicationLocation['location'][] = $location;
    }

    /**
     * @return mixed
     */
    public function getLocations(){
        return $this->replicationLocation['location'];
    }
    /**
     * @return mixed
     */
    public function getLocationTransferTypes(){
        return $this->replicationLocation['LocationTransferTypeConstraint'];
    }

    /**
     * @param $locationTransferType array
     */
    public function setLocationTransferTypes($locationTransferType){
        $this->replicationLocation['LocationTransferTypeConstraint'] = $locationTransferType;
    }


    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $this->replicationLocation = array();
        $xml = simplexml_load_string($strXml);
        if (isset($xml->Location)){
            foreach ($xml->Location as $location){
                $this->setLocations(strval($location));
            }
        }
        if (isset($xml->LocationTransferTypeConstraint)){
            $locationTypes = array();
            $objLocationTransferTypes = $xml->LocationTransferTypeConstraint;
            $i = 0;
            foreach ($objLocationTransferTypes->LocationTransferType as $type) {
                $locationTypes[$i]['location'] = strval($type->Location);
                $locationTypes[$i]['type'] = strval($type->TransferTypes->Type);
                $i++;
            }
            $this->setLocationTransferTypes($locationTypes);
        }
    }


    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ReplicationLocation></ReplicationLocation>');
        if (isset($this->replicationLocation['location'])){
            foreach ($this->replicationLocation['location'] as $location){
                $xml->addChild('Location',$location);
            }
        }
        if (isset($this->replicationLocation['LocationTransferTypeConstraint'])){
            $xmlConstraint = $xml->addChild('LocationTransferTypeConstraint');
            foreach ($this->replicationLocation['LocationTransferTypeConstraint'] as $locationTransferType){
                $xmlTransferType = $xmlConstraint->addChild('LocationTransferType');
                if (isset($locationTransferType['location'])){
                    $xmlTransferType->addChild('Location',$locationTransferType['location']);
                }
                if (isset($locationTransferType['type'])){
                    $xmlTypes = $xmlTransferType->addChild('TransferTypes');
                    $xmlTypes->addChild('Type',$locationTransferType['type']);
                }

            }
        }
        return $xml->asXML();
    }

    /**
     *  Serialize the object into xml string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->serializeToXml();
    }

}


