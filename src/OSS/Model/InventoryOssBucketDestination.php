<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class InventoryOSSBucketDestination
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryOssBucketDestination
{

    const DEST_FORMAT = 'CSV';
    private $roleArn = '';
    private $accountId = "";
    public $oSSBucketDestination = array();
    /**
     * InventoryConfig constructor.
     *
            'OSSBucketDestination'=>array(
                'Format'=>'CSV',
                'AccountId'=>'1000000000000000',
                'RoleArn'=>'acs:ram::1000000000000000:role/AliyunOSSRole',
                'Bucket'=>'acs:oss:::<bucket_name>',
                'Prefix'=>'prefix1',
                'Encryption'=>array(
                    'SSE-KMS'=>array(
                        'KeyId'=>'key1'
                    )
                )
     */
    public function __construct()
    {
        $this->oSSBucketDestination = array();
    }

    /**
     * @param $format string
     */
    public function addFormat($format)
    {
        $this->oSSBucketDestination['Format'] = $format;
    }

    /**
     * @param $roleArn string
     * @throws OssException
     */
    public function addRoleArn($roleArn)
    {
        if($this->accountId == ""){
            throw new OssException("Not account id.");
        }
        $this->roleArn = sprintf("acs:ram::%s:role/%s",$this->accountId,$roleArn);
        $this->oSSBucketDestination['RoleArn'] = $this->roleArn;
    }

    /**
     * @param $accountId string
     */
    public function addAccountId($accountId)
    {
        $this->oSSBucketDestination['AccountId'] = $accountId;
        $this->accountId = $accountId;
    }

    /**
     * @param $prefix string
     */
    public function addPrefix($prefix)
    {
        $this->oSSBucketDestination['Prefix'] = $prefix;
    }


    /**
     * @param $keyId string
     */
    public function addEncryptionKms($keyId)
    {
        $this->oSSBucketDestination['Encryption']['SSE-KMS'] = $keyId;
    }


    public function addEncryptionOss()
    {
        $this->oSSBucketDestination['Encryption']['SSE-OSS'] = '';
    }

    /**
     * @param $bucketName string
     */
    public function addBucketName($bucketName)
    {
        $this->oSSBucketDestination['Bucket'] = sprintf("acs:oss:::%s",$bucketName);
    }

}


