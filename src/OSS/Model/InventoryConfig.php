<?php

namespace OSS\Model;

use OSS\Core\OssException;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;


/**
 * Class InventoryConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfig implements XmlConfig
{
    const OBJECT_VERSION_CURRENT = 'Current';
    const OBJECT_VERSION_ALL = 'All';

    const FREQUENCY_WEEKLY = 'Weekly';
    const FREQUENCY_DAILY = 'Daily';
    
    const IS_ENABLED_TRUE = 'true';
    const IS_ENABLED_FALSE = 'false';
    
    const FIELD_SIZE = 'Size';
    const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
    const FIELD_IS_MULTIPART_UPLOADED = 'IsMultipartUploaded';
    const FIELD_ETAG = 'ETag';
    const FIELD_STORAGE_CLASS = 'StorageClass';
    const FIELD_ENCRYPTION_STATUS = 'EncryptionStatus';

    const DEST_FORMAT = 'CSV';

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $isEnabled;

    /**
     * @var InventoryConfigFilter
     */
    private $filter;

    /**
     * @var InventoryConfigOssBucketDestination
     */
    private $destination;

    /**
     * @var string
     */
    private $schedule;
    /**
     * @var string
     */
    private $includedObjectVersions;

    /**
     * @var InventoryConfigOptionalFields[]
     */
    private $optionalFields;


    /**
     * InventoryConfig constructor.
     * @param null $id
     * @param null $isEnabled
     * @param null $filter
     * @param null $destination
     * @param null $schedule
     * @param null $includedObjectVersions
     * @param null $optionalFields
     */
    public function __construct($id=null,$isEnabled=null,$schedule=null,$includedObjectVersions=null,$destination=null,$filter=null,$optionalFields=null)
    {
        $this->id = $id;
        $this->isEnabled = $isEnabled;
        $this->filter = $filter;
        $this->destination = $destination;
        $this->schedule = $schedule;
        $this->includedObjectVersions = $includedObjectVersions;
        $this->optionalFields = $optionalFields;
    }

    /**
     * @param $destination InventoryConfigOssBucketDestination
     */
    public function addDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @param $filter InventoryConfigFilter
     */
    public function addFilter($filter)
    {
        $this->filter = $filter;
    }


    /**
     * @param $optionalFields InventoryConfigOptionalFields[]
     */
    public function addOptionalFields($optionalFields){
        $this->optionalFields = $optionalFields;
    }

    /**
     * @param $schedule string
     * Schedule>Frequency
     */
    public function addSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @param $includedObjectVersions string
     */
    public function addIncludedObjectVersions($includedObjectVersions)
    {
        $this->includedObjectVersions = $includedObjectVersions;
    }

    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        $this->parseFromXmlObj($xml);
    }


    /**
     * @param $xml \SimpleXMLElement
     */
    public function parseFromXmlObj($xml)
    {
        if (!isset($xml->Id) && !isset($xml->IsEnabled) && !isset($xml->Destination) && !isset($xml->Schedule)&& !isset($xml->Filter)&& !isset($xml->IncludedObjectVersions)&& !isset($xml->OptionalFields)) return;
        if (isset($xml->Id)){
            $this->id = strval($xml->Id);
        }
        if (isset($xml->IsEnabled)){
            $this->isEnabled = strval($xml->IsEnabled);
        }
        if (isset($xml->Destination)){
            $this->parseDestination($xml->Destination);
        }
        if (isset($xml->Schedule)){
            $this->parseSchedule($xml->Schedule);
        }
        if (isset($xml->Filter)){
            $this->parseFilter($xml->Filter);
        }
        if (isset($xml->IncludedObjectVersions)){
            $this->includedObjectVersions = strval($xml->IncludedObjectVersions);
        }
        if (isset($xml->OptionalFields)){
            $this->parseOptionalFields($xml->OptionalFields);
        }
    }
    /**
     * @param $xmlDestination \SimpleXMLElement
     */
    private function parseDestination($xmlDestination){
        if (isset($xmlDestination)){
            $ossBucketDestination = $xmlDestination->OSSBucketDestination;
            if ($ossBucketDestination->Format){
                $format = strval($ossBucketDestination->Format);
            }
            if ($ossBucketDestination->AccountId){
                $accountId = strval($ossBucketDestination->AccountId);
            }
            if ($ossBucketDestination->RoleArn){
                $roleArn = strval($ossBucketDestination->RoleArn);
            }
            if ($ossBucketDestination->Bucket){
                $bucket = strval($ossBucketDestination->Bucket);
            }
            if ($ossBucketDestination->Prefix){
                $prefix = strval($ossBucketDestination->Prefix);
            }
            if ($ossBucketDestination->Encryption){
                $encryption = $ossBucketDestination->Encryption;
                if ($encryption->xpath('SSE-OSS') !== null){
                    $tmp = $encryption->xpath('SSE-OSS');
                    $ossKeyId = strval($tmp[0]->KeyId);
                }
                if ($encryption->xpath('SSE-KMS') !== null){
                    $tmp = $encryption->xpath('SSE-KMS');
                    $kmsKeyId = ($tmp[0]->KeyId);
                }
            }

            $configOssBucketDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucket,$prefix,$ossKeyId,$kmsKeyId);
            $this->addDestination($configOssBucketDestination);
        }
    }

    /**
     * @param $xmlSchedule \SimpleXMLElement
     */
    private function parseSchedule($xmlSchedule){
        if (isset($xmlSchedule)){
            if (isset($xmlSchedule->Frequency)){
                $this->addSchedule(strval($xmlSchedule->Frequency));
            }
        }
    }

    /**
     * @param $xmlFilter \SimpleXMLElement
     */
    private function parseFilter($xmlFilter){
        if (isset($xmlFilter)){
            if (isset($xmlFilter->Prefix)){
                $prefix = strval($xmlFilter->Prefix);
            }
            if (isset($xmlFilter->LastModifyBeginTimeStamp)){
                $lastModifyBeginTimeStamp = strval($xmlFilter->LastModifyBeginTimeStamp);
            }
            if (isset($xmlFilter->LastModifyEndTimeStamp)){
                $lastModifyEndTimeStamp = strval($xmlFilter->LastModifyEndTimeStamp);
            }
            if (isset($xmlFilter->LowerSizeBound)){
                $lowerSizeBound = strval($xmlFilter->LowerSizeBound);
            }
            if (isset($xmlFilter->UpperSizeBound)){
                $upperSizeBound = strval($xmlFilter->UpperSizeBound);
            }
            if (isset($xmlFilter->StorageClass)){
                $storageClass = strval($xmlFilter->StorageClass);
            }
            $configFilter = new InventoryConfigFilter($prefix,$lastModifyBeginTimeStamp,$lastModifyEndTimeStamp,$lowerSizeBound,$upperSizeBound,$storageClass);
            $this->addFilter($configFilter);
        }
    }

    /**
     * @param $xmlOptionalFields \SimpleXMLElement
     */
    private function parseOptionalFields($xmlOptionalFields){
        if (isset($xmlOptionalFields)){
            $configFields = array();
            foreach ($xmlOptionalFields->Field as $field){
                $configFields[] = new InventoryConfigOptionalFields(strval($field));
            }
            $this->addOptionalFields($configFields);
        }
    }

    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><InventoryConfiguration></InventoryConfiguration>');
        if (isset($this->id)){
            $xml->addChild("Id",$this->id);
        }
        if (isset($this->isEnabled)){
            $xml->addChild("IsEnabled",$this->isEnabled);
        }
        if (isset($this->destination)){
            $xmlDestination = $xml->addChild("Destination");
            $xmlOSSBucketDestination = $xmlDestination->addChild("OSSBucketDestination");
            $this->destination->appendToXml($xmlOSSBucketDestination);
        }
        if (isset($this->schedule)){
            $xmlSchedule = $xml->addChild("Schedule");
            $xmlSchedule->addChild("Frequency",$this->schedule);
        }
        if (isset($this->filter)){
            $xmlFilter = $xml->addChild("Filter");
            $this->filter->appendToXml($xmlFilter);
        }
        if (isset($this->includedObjectVersions)){
            $xml->addChild("IncludedObjectVersions",$this->includedObjectVersions);
        }
        if (isset($this->optionalFields)){
            $xmlOptionalFields = $xml->addChild("OptionalFields");
            foreach ($this->optionalFields as $field){
                $field->appendToXml($xmlOptionalFields);
            }

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


    /**
     * @return string|null
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIsEnabled(){
        return $this->isEnabled;
    }

    /**
     * @return InventoryConfigOssBucketDestination|null
     */
    public function getDestination(){
        return $this->destination;
    }

    /**
     * @return string
     */
    public function getSchedule(){
        return $this->schedule;
    }

    /**
     * @return InventoryConfigFilter
     */
    public function getFilter(){
        return $this->filter;
    }

    /**
     * @return string
     */
    public function getIncludedObjectVersions(){
        return $this->includedObjectVersions;
    }

    /**
     * @return InventoryConfigOptionalFields[]|null
     */
    public function getOptionalFields(){
        return $this->optionalFields;
    }
}


