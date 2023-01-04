<?php
namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class StyleConfig
 * @package OSS\Model
 */
class StyleConfig implements XmlConfig
{

    /**
     * StyleConfig constructor
     * @param string $name style name
     * @param string $content style content
     * @param string $createTime style create time
     * @param string $lastModifyTime style last modify time
     */
    public function __construct($name=null, $content=null, $createTime=null, $lastModifyTime=null)
    {
        $this->name = $name;
        $this->content = $content;
        $this->createTime = $createTime;
        $this->lastModifyTime = $lastModifyTime;
    }
    /**
     * Parse StyleConfig from the xml.
     * @param string $strXml
     * @return null
     * @throws OssException
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (isset($xml->Name)) {
            $this->name = strval($xml->Name);
        }
        if (isset($xml->Content)) {
            $this->content = strval($xml->Content);
        }
        if (isset($xml->CreateTime)) {
            $this->createTime = strval($xml->CreateTime);
        }
        if (isset($xml->LastModifyTime)) {
            $this->lastModifyTime = strval($xml->LastModifyTime);
        }
    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Style></Style>');
        if (isset($this->name)) {
            $xml->addChild('Name', $this->name);
        }
        if (isset($this->content)) {
            $xml->addChild('Content', $this->content);
        }
        if (isset($this->createTime)) {
            $xml->addChild('createTime', $this->createTime);
        }
        if (isset($this->lastModifyTime)) {
            $xml->addChild('lastModifyTime', $this->lastModifyTime);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }

    /**
     * @param \SimpleXMLElement $xmlStyleList
     */
    public function appendToXml(&$xmlStyleList)
    {
        if(isset($this->name) || isset($this->content) || isset($this->createTime) || isset($this->lastModifyTime)){
            $xmlStyle = $xmlStyleList->addChild("Style");
        }

        if (isset($this->name)){
            $xmlStyle->addChild('Name', $this->name);
        }

        if (isset($this->content)){
            $xmlStyle->addChild('Content', $this->content);
        }

        if (isset($this->createTime)){
            $xmlStyle->addChild('CreateTime', $this->createTime);
        }

        if (isset($this->lastModifyTime)){
            $xmlStyle->addChild('LastModifyTime', $this->lastModifyTime);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param string $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * @return string
     */
    public function getLastModifyTime()
    {
        return $this->lastModifyTime;
    }

    /**
     * @param string $lastModifyTime
     */
    public function setLastModifyTime($lastModifyTime)
    {
        $this->lastModifyTime = $lastModifyTime;
    }

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $createTime;

    /**
     * @var string
     */
    private $lastModifyTime;
}