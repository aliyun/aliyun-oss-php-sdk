<?php

namespace OSS\Model;

/**
 * Class ReplicationRuleProgress
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/181408.htm
 */
class ReplicationRuleProgress
{
    private $historicalObject;

    private $newObject;

    /**
     * @param $historicalObject string
     */
    public function setHistoricalObject($historicalObject)
    {
        $this->historicalObject = $historicalObject;
    }

    /**
     * @return string |null
     */
    public function getHistoricalObject(){
        return $this->historicalObject;
    }

    /**
     * @param $newObject string
     */
    public function setNewObject($newObject)
    {
        $this->newObject = $newObject;
    }

    /**
     * @return string |null
     */
    public function getNewObject(){
        return $this->newObject;
    }
    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if (isset($this->historicalObject) || isset($this->newObject)){
            $xmlProgress= $xmlRule->addChild('Progress');
        }
        if (isset($this->historicalObject)){
            $xmlProgress->addChild('HistoricalObject', $this->historicalObject);
        }
        if (isset($this->newObject)){
            $xmlProgress->addChild('NewObject', $this->newObject);
        }
    }
}


