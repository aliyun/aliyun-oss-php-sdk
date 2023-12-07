<?php

namespace OSS\Model;

/**
 * Class MetaQueryAggregation
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/419228.html
 */
class MetaQueryAggregation
{

    /**
     * @return string
     */
    public function getField(){
        return $this->field;
    }

    /**
     * @param $field string
     */
    public function setField($field){
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getOperation(){
        return $this->operation;
    }

    /**
     * @param $operation string
     */
    public function setOperation($operation){
        $this->operation = $operation;
    }

    /**
     * @return float|null
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param $value string
     */
    public function setValue($value){
        $this->value = $value;
    }

    /**
     * @return MetaQueryGroup[]
     */
    public function getGroups(){
        return $this->groups;
    }


    /**
     * @param $group MetaQueryGroup
     */
    public function addGroup($group){
        $this->groups[] = $group;
    }

    /**
     * @param \SimpleXMLElement $xmlAggregation
     */
    public function appendToXml(&$xmlAggregation)
    {

        if (isset($this->field)){
            $xmlAggregation->addChild('Field',$this->field);
        }
        if (isset($this->operation)){
            $xmlAggregation->addChild('Operation',$this->operation);
        }
        if (isset($this->groups)){
            $xmlGroups = $xmlAggregation->addChild('Groups',$this->operation);
            foreach ($this->groups as $group){
                $xmlGroup = $xmlGroups->addChild('Group');
                $group->appendToXml($xmlGroup);
            }
        }

    }


    /**
     * @var string
     */
    private $field;
    /**
     * @var string
     */
    private $operation;
    /**
     * @var float
     */
    private $value;
    /**
     * @var MetaQueryGroup[]
     */
    private $groups;
}