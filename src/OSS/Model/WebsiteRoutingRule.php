<?php

namespace OSS\Model;

/**
 * Class WebsiteRoutingRules
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31962.html
 */
class WebsiteRoutingRule {
    /**
     * @var WebsiteCondition
     */
    private $condition;
    /**
     * @var WebsiteRedirect
     */
    private $redirect;
    /**
     * @var int
     */
    private $ruleNumber;

    /**
     * @param int $number
     */
    public function setNumber($number){
        $this->ruleNumber = $number;
    }

    /**
     * @return int
     */
    public function getRuleNumber(){
        return $this->ruleNumber;
    }

    /**
     * @param $condition WebsiteCondition
     */
    public function setCondition($condition){
        $this->condition = $condition;
    }

    /**
     * @return WebsiteCondition
     */
    public function getCondition(){
        return $this->condition;
    }

    /**
     * @param $redirect WebsiteRedirect
     */
    public function setRedirect($redirect){
        $this->redirect = $redirect;
    }

    /**
     * @return WebsiteRedirect
     */
    public function getRedirect(){
        return $this->redirect;
    }

    /**
     * @param \SimpleXMLElement $xmlRoutingRule
     */
    public function appendToXml(&$xmlRoutingRule)
    {
        if (isset($this->ruleNumber)){
            $xmlRoutingRule->addChild('RuleNumber', $this->ruleNumber);
        }
        if (isset($this->condition)){
            $xmlCondition = $xmlRoutingRule->addChild('Condition');
            $this->condition->appendToXml($xmlCondition);

        }
        if (isset($this->redirect)){
            $xmlRedirect = $xmlRoutingRule->addChild('Redirect');
            $this->redirect->appendToXml($xmlRedirect);
        }
    }

}