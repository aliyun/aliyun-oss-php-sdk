<?php

namespace OSS\Model;

/**
 * Class WebsiteMirrorHeadersSet
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31970.html
 */
class WebsiteMirrorHeadersSet {

    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $value;

    /**
     * @param $key string
     */
    public function setKey($key){
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(){
        return $this->key;
    }
    /**
     * @param $value string
     */
    public function setValue($value){
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param \SimpleXMLElement $xmlSet
     */
    public function appendToXml(&$xmlSet)
    {
        if (isset($this->key)){
            $xmlSet->addChild('Key', $this->key);
        }
        if (isset($this->value)){
            $xmlSet->addChild('Value', $this->value);
        }
    }

}