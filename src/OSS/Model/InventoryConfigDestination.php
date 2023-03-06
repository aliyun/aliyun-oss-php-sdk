<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class InventoryConfigDestination
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfigDestination
{


    /**
     * @var InventoryConfigOssBucketDestination
     */
    private $ossBucketDestination;

    /**
     * InventoryConfigDestination constructor.
     * @param null $ossBucketDestination
     */
    public function __construct($ossBucketDestination=null)
    {
        $this->ossBucketDestination = $ossBucketDestination;
    }

    /**
     * @param $xmlDestination \SimpleXMLElement
     */
    public function appendToXml(&$xmlDestination){
        if (isset($this->ossBucketDestination)){
            $xmlOSSBucketDestination = $xmlDestination->addChild("OSSBucketDestination");
            $this->ossBucketDestination->appendToXml($xmlOSSBucketDestination);
        }
    }
}


