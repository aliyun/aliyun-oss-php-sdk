<?php

namespace OSS\Model;

/**
 * Class WebsiteIncludeHeader
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31970.html
 */
class WebsiteIncludeHeader {

    private $key;

    private $equals;

    /**
     * @param $key string
     */
    public function setKey($key){
        $this->key = $key;
    }

    /**
     * @param $equals string
     */
    public function setEquals($equals){
        $this->equals = $equals;
    }


    /**
     * @return string
     */
    public function getKey(){
        return $this->key;
    }

    /**
     * @return string
     */
    public function getEquals(){
        return $this->equals;
    }


    /**
     * @param \SimpleXMLElement $xmlIncludeHeader
     */
    public function appendToXml(&$xmlIncludeHeader)
    {
        if (isset($this->key)){
            $xmlIncludeHeader->addChild('Key', $this->key);
        }
        if (isset($this->equals)){
            $xmlIncludeHeader->addChild('Equals', $this->equals);
        }
    }

}