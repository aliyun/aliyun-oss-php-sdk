<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class LifecycleConfig
 * @package OSS\Model
 */
class LifecycleConfig implements XmlConfig
{
    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $this->rules = array();
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Rule)) return;
        $this->parseRule($xml->Rule);
    }

    /**
     * @param $xmlRules
     */
    private function parseRule($xmlRules){
        if ($xmlRules){
            foreach ($xmlRules as $rule){
                $lifecycleRule = new LifecycleRule();
                $lifecycleRule->parseFromXml($rule);
                $this->rules[] = $lifecycleRule;
            }
        }

    }

    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><LifecycleConfiguration></LifecycleConfiguration>');
        foreach ($this->rules as $rule) {
            $xmlRule = $xml->addChild('Rule');
            $rule->appendToXml($xmlRule);
        }
        return $xml->asXML();
    }

    /**
     *
     * Add a LifecycleRule
     *
     * @param LifecycleRule $lifecycleRule
     * @throws OssException
     */
    public function addRule($lifecycleRule)
    {
        if (!isset($lifecycleRule)) {
            throw new OssException("lifecycleRule is null");
        }
        $this->rules[] = $lifecycleRule;
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

    /**
     * Get all lifecycle rules.
     *
     * @return LifecycleRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @var LifecycleRule[]
     */
    private $rules;
}


