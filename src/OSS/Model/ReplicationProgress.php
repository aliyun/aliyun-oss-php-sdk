<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class BucketReplicationProgress
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/181411.htm
 */
class ReplicationProgress
{

    private $rule;

    /**
     * @param $rule ReplicationRule
     */
    public function addRule($rule){
        $this->rule = $rule;
    }

    /**
     * @return ReplicationRule
     */
    public function getRule(){
        return $this->rule;
    }
    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
     */

    /**
     * @param $strXml
     * @return false||string|void|null
     */
    public function parseFromXml($strXml)
    {
        $this->rule = array();
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Rule)) return;
        $this->parseRule($xml->Rule);
    }

    /**
     * @param $rules
     */
    private function parseRule($rule)
    {
        $replicationRule = new ReplicationRule();

        if (isset($rule->ID)){
            $replicationRule->setId(strval($rule->ID));
        }
        if (isset($rule->PrefixSet)){
            foreach ($rule->PrefixSet->Prefix as $prefix){
                $replicationRule->setPrefixSet(strval($prefix));
            }
        }
        if (isset($rule->Action)){
            $replicationRule->setAction(strval($rule->Action));
        }
        if (isset($rule->Destination)){
            $this->parseDestination($rule->Destination,$replicationRule);
        }
        if (isset($rule->TransferType)){
            $replicationRule->setSyncRole(strval($rule->TransferType));
        }
        if (isset($rule->Status)){
            $replicationRule->setStatus(strval($rule->Status));
        }
        if (isset($rule->HistoricalObjectReplication)){
            $replicationRule->setHistoricalObjectReplication(strval($rule->HistoricalObjectReplication));
        }
        if (isset($rule->SyncRole)){
            $replicationRule->setSyncRole(strval($rule->SyncRole));
        }
        if (isset($rule->Progress)){
            $this->parseProgress($rule->Progress,$replicationRule);
        }
        $this->addRule($replicationRule);
    }

    /**
     * @param $destination \SimpleXMLElement
     * @param $replicationRule ReplicationRule
     */
    private function parseDestination($destination,&$replicationRule)
    {
        if(isset($destination)){
            $replicationDestination = new ReplicationDestination();
            if (isset($destination->Bucket)){
                $replicationDestination->setBucket(strval($destination->Bucket));
            }
            if (isset($destination->Location)){
                $replicationDestination->setLocation(strval($destination->Location));
            }
            if (isset($destination->TransferType)){
                $replicationDestination->setTransferType(strval($destination->TransferType));
            }
            $replicationRule->addDestination($replicationDestination);
        }

    }

    /**
     * @param $progress \SimpleXMLElement
     * @param $replicationRule ReplicationRule
     */
    private function parseProgress($progress,&$replicationRule)
    {
        if(isset($progress)){
            $replicationRuleProgress = new ReplicationRuleProgress();
            if (isset($progress->HistoricalObject)){
                $replicationRuleProgress->setHistoricalObject(strval($progress->HistoricalObject));
            }
            if (isset($progress->NewObject)){
                $replicationRuleProgress->setNewObject(strval($progress->NewObject));
            }
            $replicationRule->addProgress($replicationRuleProgress);
        }
    }

    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ReplicationProgress></ReplicationProgress>');
        $xmlRule = $xml->addChild('Rule');
        $this->rule->appendToXml($xmlRule);
        return $xml->asXML();
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
}


