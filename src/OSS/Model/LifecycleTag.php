<?php

namespace OSS\Model;


/**
 * Class LifecycleTag
 * @package OSS\Model
 *
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/PutBucketLifecycle.html
 */
class LifecycleTag
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
     * LifecycleTag constructor
     * @param $key string
     * @param $value string
     */
    public function __construct($key=null, $value=null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get Key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key
     * @param $key string
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     * @param $value string
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if(isset($this->key) || isset($this->value)){
            $xmlTag = $xmlRule->addChild("Tag");
        }

        if (isset($this->key)){
            $xmlTag->addChild('Key', $this->key);
        }

        if (isset($this->value)){
            $xmlTag->addChild('Value', $this->value);
        }

    }
}