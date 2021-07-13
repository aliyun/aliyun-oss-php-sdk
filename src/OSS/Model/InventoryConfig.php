<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class BucketInventoryConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfig implements XmlConfig
{

    private $configs = array();
    public function __construct($configs=null)
    {
        $this->configs = $configs;
    }
    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
     */
    public function parseFromXml($strXml)
    {

    }


    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {
        return $xml = $this->arrToXml($this->configs,null,null,'InventoryConfiguration');
    }

    /**
     * array turn to xml
     * @param $arr
     * @param $dom
     * @param $item
     * @param string $rootNode
     * @return string
     */
    public function arrToXml($arr,$dom,$item,$rootNode="Configuration")
    {
        if (!$dom) {
            $dom = new \DOMDocument("1.0",'utf-8');
        }
        if (!$item) {
            $item = $dom->createElement($rootNode);
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            if(!is_string($key)){
                continue;
            }
            if($key != 'Field'){
                $node = $dom->createElement($key);
                $item->appendChild($node);
            }else{
                foreach ($val as $value){
                    $node = $dom->createElement("Field",$value);
                    $item->appendChild($node);
                }
            }
            if (!is_array($val)) {
                if(is_bool($val)){
                    if($val == true){
                        $text = $dom->createTextNode('true');
                    }else{
                        $text = $dom->createTextNode('false');
                    }

                }else{
                    $text = $dom->createTextNode($val);
                }
                $node->appendChild($text);
            } else {
                $this->arrToXml($val, $dom, $node);
            }
        }
        return $dom->saveXML();
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


