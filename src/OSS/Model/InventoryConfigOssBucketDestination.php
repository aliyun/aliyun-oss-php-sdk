<?php

namespace OSS\Model;

/**
 * Class InventoryOSSBucketDestination
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfigOssBucketDestination
{

    const DEST_FORMAT = 'CSV';

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $roleArn;
    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $kmsKeyId;

    private $ossKeyId;
    public function __construct($format=null,$accountId=null,$roleArn=null,$bucket=null,$prefix=null,$ossKeyId=null,$kmsKeyId=null)
    {
        $this->format = $format;
        $this->accountId = $accountId;
        $this->roleArn = $roleArn;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
        $this->ossKeyId = $ossKeyId;
        $this->kmsKeyId = $kmsKeyId;
    }

    /**
     * @return string|null
     */
    public function getFormat(){
        return $this->format;
    }

    /**
     * @return string|null
     */
    public function getAccountId(){
        return $this->accountId;
    }

    /**
     * @return string|null
     */
    public function getPrefix(){
        return $this->prefix;
    }

    /**
     * @return string|null
     */
    public function getRoleArn(){
        return $this->roleArn;
    }

    /**
     * @return string|null
     */
    public function getBucket(){
        return $this->bucket;
    }

    /**
     * @return string|null
     */
    public function getKmsId(){
        return $this->kmsKeyId;
    }

    /**
     * @return string|null
     */
    public function getOssId(){
        return $this->ossKeyId;
    }

    /**
     * @param $keyId string
     */
    public function addKmsId($keyId)
    {
        $this->kmsKeyId = $keyId;
    }

    /**
     * @param $keyId string
     */
    public function addOssId($keyId)
    {
        $this->ossKeyId = $keyId;
    }

    /**
     * @param $xmlOSSBucketDestination \SimpleXMLElement
     */
    public function appendToXml(&$xmlOSSBucketDestination){
        if ($this->format){
            $xmlOSSBucketDestination->addChild("Format",$this->format);
        }
        if ($this->accountId){
            $xmlOSSBucketDestination->addChild("AccountId",$this->accountId);
        }
        if ($this->roleArn){
            $xmlOSSBucketDestination->addChild("RoleArn",$this->roleArn);
        }
        if ($this->bucket){
            $xmlOSSBucketDestination->addChild("Bucket",$this->bucket);
        }
        if ($this->prefix){
            $xmlOSSBucketDestination->addChild("Prefix",$this->prefix);
        }
        $xmlEncryption = $xmlOSSBucketDestination->addChild("Encryption");
        if ($this->ossKeyId || $this->kmsKeyId){
            if ($this->ossKeyId){
                $xmlSse = $xmlEncryption->addChild("SSE-OSS");
                $xmlSse->addChild("KeyId",$this->ossKeyId);
            }
            if ($this->kmsKeyId){
                $xmlSse = $xmlEncryption->addChild("SSE-KMS");
                $xmlSse->addChild("KeyId",$this->kmsKeyId);
            }
        }else{
            $xmlEncryption->addChild("SSE-OSS");
        }
    }

}


