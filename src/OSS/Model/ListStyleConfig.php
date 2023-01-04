<?php
namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class ListStyleConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/469894.html
 */
class ListStyleConfig implements XmlConfig
{
    /**
     * Parse StyleConfig from the xml.
     * @param string $strXml
     * @return null
     * @throws OssException
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        $this->styleList = array();
        foreach ($xml as $style) {
            if (count(get_object_vars($style)) == 0){
                continue;
            }
            $name = isset($style->Name) ? strval($style->Name) : "";
            $content = isset($style->Content) ? strval($style->Content) : "";
            $createTime = isset($style->CreateTime) ? strval($style->CreateTime) : "";
            $lastModifyTime = isset($style->LastModifyTime) ? strval($style->LastModifyTime) : "";
            $styleNode = new StyleConfig($name,$content,$createTime,$lastModifyTime);
            $this->styleList[] = $styleNode;
        }

    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><StyleList></StyleList>');
        foreach ($this->styleList as $style) {
            $style->appendToXml($xml);
        }
        return $xml->asXML();
    }


    public function __toString()
    {
        return $this->serializeToXml();
    }

    /**
     * Get bucket style list.
     *
     * @return StyleConfig[]
     */
    public function getStyleList()
    {
        return $this->styleList;
    }

    /**
     * @var StyleConfig[]
     */
    private $styleList;
}