<?php

namespace OSS\Model;

/**
 * Class ReplicationRule
 * @package OSS\Model
 * @url https://help.aliyun.com/document_detail/181408.html
 */
class ReplicationRule {
    const ACTION_ALL = 'ALL';
    const ACTION_PUT = 'PUT';

    private $id;
    private $rtc;
    private $prefix;
    private $action;
    /**
     * @var ReplicationDestination
     */
    private $destination;
    private $transferType;
    private $historicalObjectReplication;
    private $syncRole;
    /**
     * @var ReplicationSourceSelectionCriteria
     */
    private $sourceSelectionCriteria;
    /**
     * @var ReplicationEncryptionConfiguration
     */
    private $encryptionConfiguration;
    private $status;

    /**
     * @var ReplicationRuleProgress
     */
    private $progress;

    /**
     * @param $id string
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param string $status  enabled|disabled
     */
    public function setRTC($status){
        $this->rtc = $status;
    }

    /**
     * @return string enabled|disabled
     */
    public function getRTC(){
        return $this->rtc;
    }

    /**
     * @param $prefix string
     */
    public function setPrefixSet($prefix){
        $this->prefix[] = $prefix;
    }

    /**
     * @return array|null
     */
    public function getPrefixSet(){
        return $this->prefix;
    }


    /**
     * @param $action string ALL|PUT
     */
    public function setAction($action){
        $this->action= $action;
    }

    /**
     * @return string|null
     */
    public function getAction(){
        return $this->action;
    }


    /**
     * @param $status string starting|doing|closing
     */
    public function setStatus($status){
        $this->status= $status;
    }


    /**
     * @return string |null
     */
    public function getStatus(){
        return $this->status;
    }
    /**
     * @param $destination ReplicationDestination
     */
    public function addDestination($destination){
        $this->destination= $destination;
    }

    /**
     * @return ReplicationDestination
     */
    public function getDestination(){
        return $this->destination;
    }

    /**
     * @param $transferType string
     */
    public function setTransferType($transferType){
        $this->transferType= $transferType;
    }

    /**
     * @return string
     */
    public function getTransferType(){
        return $this->transferType;
    }

    /**
     * @param $status string enabled | disabled
     */
    public function setHistoricalObjectReplication($status){
        $this->historicalObjectReplication= $status;
    }

    /**
     * @return string enabled | disabled
     */
    public function getHistoricalObjectReplication(){
        return $this->historicalObjectReplication;
    }

    /**
     * @param $syncRole string
     */
    public function setSyncRole($syncRole){
        $this->syncRole= $syncRole;
    }

    /**
     * @return string |null
     */
    public function getSyncRole(){
        return $this->syncRole;
    }

    /**
     * @param $sourceSelectionCriteria ReplicationSourceSelectionCriteria
     */
    public function addSourceSelectionCriteria($sourceSelectionCriteria){
        $this->sourceSelectionCriteria= $sourceSelectionCriteria;
    }

    /**
     * @return ReplicationSourceSelectionCriteria
     */
    public function getSourceSelectionCriteria(){
        return $this->sourceSelectionCriteria;
    }

    /**
     * @param $replicationEncryptionConfiguration ReplicationEncryptionConfiguration
     */
    public function addEncryptionConfiguration($replicationEncryptionConfiguration){
        $this->encryptionConfiguration= $replicationEncryptionConfiguration;
    }

    /**
     * @return ReplicationEncryptionConfiguration
     */
    public function getEncryptionConfiguration(){
        return $this->encryptionConfiguration;
    }

    /**
     * @param $replicationRuleProgress ReplicationRuleProgress
     */
    public function addProgress($replicationRuleProgress){
        $this->progress= $replicationRuleProgress;
    }

    /**
     * @return ReplicationRuleProgress
     */
    public function getProgress(){
        return $this->progress;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if (isset($this->id)){
            $xmlRule->addChild('ID', $this->id);
        }
        if (isset($this->prefix)){
            $xmlPrefixSet = $xmlRule->addChild('PrefixSet');
            foreach ($this->prefix as $prefix){
                $xmlPrefixSet->addChild('Prefix',$prefix);
            }
        }
        if (isset($this->action)){
            $xmlRule->addChild('Action', $this->action);
        }
        if (isset($this->destination)){
            $this->destination->appendToXml($xmlRule);
        }
        if (isset($this->status)){
            $xmlRule->addChild('Status', $this->status);
        }
        if (isset($this->historicalObjectReplication)){
            $xmlRule->addChild('HistoricalObjectReplication', $this->historicalObjectReplication);
        }
        if (isset($this->sourceSelectionCriteria)){
            $status = $this->sourceSelectionCriteria->getStatus();
            if (isset($status)){
                $xmlSourceSelectionCriteria = $xmlRule->addChild('SourceSelectionCriteria');
                $xmlSseKmsEncryptedObjects = $xmlSourceSelectionCriteria->addChild('SseKmsEncryptedObjects');
                $xmlSseKmsEncryptedObjects->addChild('Status', $status);
            }
        }
        if (isset($this->syncRole)){
            $xmlRule->addChild('SyncRole', $this->syncRole);
        }
        if (isset($this->encryptionConfiguration)){
            $kmsId = $this->encryptionConfiguration->getReplicaKmsKeyID();
            if (isset($kmsId)){
                $xmlEncryptionConfiguration = $xmlRule->addChild('EncryptionConfiguration');
                $xmlEncryptionConfiguration->addChild('ReplicaKmsKeyID', $kmsId);
            }
        }
        if (isset($this->rtc)){
            $xmlRtc = $xmlRule->addChild('RTC');
            $xmlRtc->addChild('Status',$this->rtc);
        }
        if (isset($this->progress)){
            $this->progress->appendToXml($xmlRule);
        }
    }

}