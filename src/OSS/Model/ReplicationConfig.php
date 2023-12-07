<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class ReplicationConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class ReplicationConfig implements XmlConfig
{

    /**
     * @var ReplicationRule[]
     */
    private $rule;

    /**
     * @param $rule ReplicationRule
     */
    public function addRule($rule){
        $this->rule[] = $rule;
    }

    /**
     * @return ReplicationRule[]
     */
    public function getRules(){
        return $this->rule;
    }

    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
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
    private function parseRule($rules)
    {
        if(isset($rules)){
            foreach ($rules as $rule){
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
                if (isset($rule->Status)){
                    $replicationRule->setStatus(strval($rule->Status));
                }
                if (isset($rule->HistoricalObjectReplication)){
                    $replicationRule->setHistoricalObjectReplication(strval($rule->HistoricalObjectReplication));
                }
                if (isset($rule->SyncRole)){
                    $replicationRule->setSyncRole(strval($rule->SyncRole));
                }
                if (isset($rule->SourceSelectionCriteria)){
                    $this->parseSourceSelectionCriteria($rule->SourceSelectionCriteria,$replicationRule);
                }
                if (isset($rule->EncryptionConfiguration)){
                    $this->parseEncryptionConfiguration($rule->EncryptionConfiguration,$replicationRule);
                }
                if (isset($rule->RTC->Status)){
                    $replicationRule->setRTC(strval($rule->RTC->Status));
                }
                $this->addRule($replicationRule);
            }
        }

    }

    /**
     * @param $destination array
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
     * @param $sourceSelectionCriteria array
     * @param $replicationRule ReplicationRule
     */
    private function parseSourceSelectionCriteria($sourceSelectionCriteria,&$replicationRule)
    {
        if(isset($sourceSelectionCriteria)){
            $replicationSourceSelectionCriteria = new ReplicationSourceSelectionCriteria();
            if (isset($sourceSelectionCriteria->SseKmsEncryptedObjects->Status)){
                $replicationSourceSelectionCriteria->setStatus(strval($sourceSelectionCriteria->SseKmsEncryptedObjects->Status));
            }
            $replicationRule->addSourceSelectionCriteria($replicationSourceSelectionCriteria);
        }

    }

    /**
     * @param $encryptionConfiguration array
     * @param $replicationRule ReplicationRule
     */
    private function parseEncryptionConfiguration($encryptionConfiguration,&$replicationRule)
    {
        if(isset($encryptionConfiguration)){
            $replicationEncryptionConfiguration = new ReplicationEncryptionConfiguration();
            if (isset($encryptionConfiguration->ReplicaKmsKeyID)){
                $replicationEncryptionConfiguration->setReplicaKmsKeyID(strval($encryptionConfiguration->ReplicaKmsKeyID));
            }
            $replicationRule->addEncryptionConfiguration($replicationEncryptionConfiguration);
        }
    }

    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ReplicationConfiguration></ReplicationConfiguration>');
        foreach ($this->rule as $rule) {
            $xmlRule = $xml->addChild('Rule');
            $rule->appendToXml($xmlRule);
        }
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


