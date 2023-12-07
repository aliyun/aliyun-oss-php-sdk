<?php

namespace OSS\Model;
use OSS\Core\OssException;
/**
 * Class LiveChannelInfo
 * @package OSS\Model
 *
 */
class LiveChannelInfo
{
    public function __construct($name = null, $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->publishUrls = array();
        $this->playUrls = array();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPublishUrls()
    {
        return $this->publishUrls;
    }

    public function addPublishUrls($url){
        $this->publishUrls[] = $url;
    }

    public function getPlayUrls()
    {
        return $this->playUrls;
    }

    public function addPlayUrls($url){
        $this->playUrls[] = $url;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function setLastModified($lastModified)
    {
       $this->lastModified = $lastModified;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function parseFromXmlNode($xml)
    {
        if (isset($xml->Name)) {
            $this->name = strval($xml->Name);
        }

        if (isset($xml->Description)) {
            $this->description = strval($xml->Description);
        }

        if (isset($xml->Status)) {
            $this->status = strval($xml->Status);
        }

        if (isset($xml->LastModified)) {
            $this->lastModified = strval($xml->LastModified);
        }

        if (isset($xml->PublishUrls)) {
            foreach ($xml->PublishUrls as $url) {
                $this->publishUrls[] = strval($url->Url);
            }
        }

        if (isset($xml->PlayUrls)) {
            foreach ($xml->PlayUrls as $url) {
                $this->playUrls[] = strval($url->Url);
            }
        }
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        $this->parseFromXmlNode($xml);
    }

    public function serializeToXml()
    {
        throw new OssException("Not implemented.");
    }
    
    private $name;
    private $description;
    private $publishUrls;
    private $playUrls;
    private $status;
    private $lastModified;
}
