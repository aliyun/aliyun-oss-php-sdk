<?php

namespace OSS\Model;


/**
 * Class CallbackPolicyConfig
 * @package OSS\Model
 *
 */
class CallbackPolicyConfig implements XmlConfig
{
    /**
     * @var CallbackPolicyItem[]
     */
    private $policyItem;

    /**
     * @param string $strXml
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->PolicyItem)) return;
        foreach ($xml->PolicyItem as $item){
            $name = strval($item->PolicyName);
            $callback = strval($item->Callback);
            $callbackVar = strval($item->CallbackVar);
            $policy = new CallbackPolicyItem($name,$callback,$callbackVar);
            $this->addPolicyItem($policy);
        }
    }

    /**
     * @param $policyItem CallbackPolicyItem
     */
    public function addPolicyItem($policyItem){
        $this->policyItem[] = $policyItem;
    }


    /**
     * @return CallbackPolicyItem[]
     */
    public function getPolicyItem(){
        return $this->policyItem;
    }


    /**
     * serialize the RefererConfig object into xml string
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><BucketCallbackPolicy></BucketCallbackPolicy>');

        if (isset($this->policyItem)){
            foreach ($this->policyItem as $policyItem) {
                $xmlItem= $xml->addChild('PolicyItem');
                $policyItem->appendToXml($xmlItem);
            }
        }
        return $xml->asXML();
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->serializeToXml();
    }
}