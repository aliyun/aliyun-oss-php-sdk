<?php

namespace OSS\Model;


/**
 * Class PolicyItem
 * @package OSS\Model
 */
class CallbackPolicyItem{

    /**
     * @var string
     */
    private $policyName;
    /**
     * @var string
     */
    private $callback;
    /**
     * @var string
     */
    private $callbackVar;


    /**
     * CallbackPolicyItem constructor.
     * @param $policyName
     * @param $callback
     * @param null $callbackVar
     */
    public function __construct($policyName, $callback, $callbackVar='')
    {
        $this->policyName = $policyName;
        $this->callback = $callback;
        $this->callbackVar = $callbackVar;
    }

    public function getPolicyName()
    {
        return $this->policyName;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getCallbackVar()
    {
        return $this->callbackVar;
    }

    /**
     * @param \SimpleXMLElement $xmlPolicyItem
     */
    public function appendToXml(&$xmlPolicyItem)
    {
        if (isset($this->policyName)){
            $xmlPolicyItem->addChild('PolicyName', $this->policyName);
        }
        if (isset($this->callback)){
            $xmlPolicyItem->addChild('Callback', $this->callback);
        }
        if (isset($this->callbackVar)){
            $xmlPolicyItem->addChild('CallbackVar', $this->callbackVar);
        }
    }


}