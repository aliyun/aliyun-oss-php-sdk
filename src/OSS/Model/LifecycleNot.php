<?php

namespace OSS\Model;


/**
 * Class LifecycleFilterNot
 * @package OSS\Model
 *
 */
class LifecycleNot
{

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var LifecycleTag
     */
    private $tag;

    /**
     * LifecycleFilterNot constructor.
     * @param string|null $prefix
     * @param LifecycleTag|null $tag
     */
    public function __construct($prefix=null,$tag=null)
    {
        $this->prefix = $prefix;
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get Tag
     *
     * @return LifecycleTag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set Tag
     * @param $tag LifecycleTag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        $xmlNot = $xmlRule->addChild("Not");
        $xmlNot->addChild('Prefix', $this->prefix);
        if (isset($this->tag)){
            $this->tag->appendToXml($xmlNot);
        }
    }
}