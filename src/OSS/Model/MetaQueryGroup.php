<?php

namespace OSS\Model;

/**
 * Class MetaQueryGroup
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/419228.html
 */
class MetaQueryGroup
{

    /**
     * @var float
     */
    private $count;
    /**
     * @var string
     */
    private $value;

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
     * @return float
     */
    public function getCount(){
        return $this->count;
    }

    /**
     * @param $count float
     */
    public function setCount($count){
        $this->count = $count;
    }

    /**
     * @param \SimpleXMLElement $xmlGroup
     */
    public function appendToXml(&$xmlGroup)
    {
        if (isset($this->value)){
            $xmlGroup->addChild('Value', $this->value);
        }
        if (isset($this->count)){
            $xmlGroup->addChild('Count', $this->count);
        }
    }
}