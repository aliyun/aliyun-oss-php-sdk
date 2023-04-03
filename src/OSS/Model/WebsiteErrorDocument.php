<?php

namespace OSS\Model;

/**
 * Class WebsiteErrorDocument
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31962.html
 */
class WebsiteErrorDocument {
    /**
     * @var string|null
     */
    private $key;
    /**
     * @var int|null
     */
    private $httpStatus;


    public function __construct($key=null,$httpStatus=null)
    {
        $this->key = $key;
        $this->httpStatus = $httpStatus;
    }

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
     * @param $httpStatus int
     */
    public function setHttpStatus($httpStatus){
        $this->httpStatus = $httpStatus;
    }

    /**
     * @return string
     */
    public function getHttpStatus(){
        return $this->httpStatus;
    }

    /**
     * @param \SimpleXMLElement $xmlErrorDocument
     */
    public function appendToXml(&$xmlErrorDocument)
    {
        if (isset($this->key)){
            $xmlErrorDocument->addChild('Key', $this->key);
        }
        if (isset($this->httpStatus)){
            $xmlErrorDocument->addChild('HttpStatus', $this->httpStatus);
        }
    }

}