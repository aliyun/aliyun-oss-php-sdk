<?php

namespace OSS\Model;

/**
 * Class ListBucketWithReservedCapacity
 * @package OSS\Model
 */
class ListBucketWithReservedCapacity
{
    /**
     * @var array
     */
    private $bucketList;

     /**
      * @var string
      */
    private $instanceId;

    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->InstanceId)) return;
        if (isset($xml->InstanceId)){
            $this->instanceId = strval($xml->InstanceId);
        }
        if (isset($xml->BucketList->Bucket)){
            foreach ($xml->BucketList->Bucket as $bucket){
                $this->bucketList[] = strval($bucket);
            }
        }
    }

    /**
     * @return array
     */
    public function getBucketList(){
        return $this->bucketList;
    }

    /**
     * @return string
     */
    public function getInstanceId() {
        return $this->instanceId;
    }
}