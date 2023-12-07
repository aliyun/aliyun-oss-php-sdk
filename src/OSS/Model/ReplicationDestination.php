<?php

namespace OSS\Model;

/**
 * Class ReplicationDestination
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/181408.htm
 */
class ReplicationDestination
{
    private $bucket;

    private $location;

    private $transferType;

    /**
     * @param $bucket string bucket name
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return string |null
     */
    public function getBucket(){
        return $this->bucket;
    }

    /**
     * @param $location string
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string |null
     */
    public function getLocation(){
        return $this->location;
    }
    /**
     * @param $transferType string
     */
    public function setTransferType($transferType)
    {
        $this->transferType = $transferType;
    }

    /**
     * @return string |null
     */
    public function getTransferType(){
        return $this->transferType;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if (isset($this->bucket) || isset($this->location) || isset($this->transferType)){
            $xmlDestination = $xmlRule->addChild('Destination');
        }
        if (isset($this->bucket)){
            $xmlDestination->addChild('Bucket', $this->bucket);
        }
        if (isset($this->location)){
            $xmlDestination->addChild('Location', $this->location);
        }
        if (isset($this->transferType)){
            $xmlDestination->addChild('TransferType', $this->transferType);
        }
    }
}


