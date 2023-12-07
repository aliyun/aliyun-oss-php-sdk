<?php

namespace OSS\Model;
/**
 * Class PutLiveChannel
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/44294.html
 */
class PutLiveChannel implements XmlConfig
{

    /**
     * @var array
     */
    private $publishUrls;
    /**
     * @var array
     */
    private $playUrls;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description string
     */
    public function setDescription($description){
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getPublishUrls()
    {
        return $this->publishUrls;
    }

    /**
     * @return array
     */
    public function getPlayUrls()
    {
        return $this->playUrls;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
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

    public function serializeToXml()
    {

        $strXml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<CreateLiveChannelResult>
</CreateLiveChannelResult>
EOF;
        $xml = new \SimpleXMLElement($strXml);
        if (isset($this->publishUrls)) {
            $xmlPublishUrls = $xml->addChild('PublishUrls');
            foreach ($this->publishUrls as $url){
                $xmlPublishUrls->addChild("Url",$url);
            }
        }
        if (isset($this->playUrls)) {
            $xmlPlayUrls = $xml->addChild('PlayUrls');
            foreach ($this->playUrls as $url){
                $xmlPlayUrls->addChild("Url",$url);
            }
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }
}
