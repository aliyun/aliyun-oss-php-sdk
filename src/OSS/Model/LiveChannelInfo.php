<?php

namespace OSS\Model;
/**
 * Class LiveChannelInfo
 * @package OSS\Model
 *
 */
class LiveChannelInfo implements XmlConfig
{
    private $id;
    private $description;
    private $publishUrls;
    private $playUrls;
    private $status;
    private $lastModified;

    public function __construct($id = null, $description = null)
    {
        $this->id = $id;
        $this->description = $description;
        $this->publishUrls = array();
        $this->playUrls = array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPublishUrls()
    {
        return $this->publishUrls;
    }

    public function getPlayUrls()
    {
        return $this->playUrls;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getLastModified()
    {
        return $this->lastModified;
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
        if (isset($xml->Id)) {
            $this->id = strval($xml->Id);
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
}
