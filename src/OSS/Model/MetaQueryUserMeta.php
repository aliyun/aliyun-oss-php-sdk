<?php

namespace OSS\Model;

/**
 * Class DoMetaQuery
 * @package OSS\Model
 */
class MetaQueryUserMeta
{

    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getKey(){
        return $this->key;
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
    public function getValue(){
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue($value){
        $this->value = $value;
    }


    /**
     * @param \SimpleXMLElement $xmlOssUserMeta
     */
    public function appendToXml(&$xmlOssUserMeta)
    {
        if (isset($this->key) || isset($this->value)){
            $xmlUserMeta = $xmlOssUserMeta->addChild('UserMeta');
        }
        if (isset($this->key)){
            $xmlUserMeta->addChild('Key', $this->key);
        }
        if (isset($this->value)){
            $xmlUserMeta->addChild('Value', $this->value);
        }
    }



}