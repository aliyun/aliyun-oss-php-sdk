<?php

namespace OSS\Model;

/**
 * Class MetaQueryTagging
 * @package OSS\Model
 */
class MetaQueryTagging
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
     * @param \SimpleXMLElement $xmlOssTagging
     */
    public function appendToXml(&$xmlOssTagging)
    {
        if (isset($this->key) || isset($this->value)){
            $xmlTagging = $xmlOssTagging->addChild('Tagging');
        }
        if (isset($this->key)){
            $xmlTagging->addChild('Key', $this->key);
        }
        if (isset($this->value)){
            $xmlTagging->addChild('Value', $this->value);
        }
    }
}