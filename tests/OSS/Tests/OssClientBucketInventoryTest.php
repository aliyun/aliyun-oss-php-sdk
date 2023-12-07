<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\InventoryConfig;
use OSS\Model\InventoryConfigFilter;
use OSS\Model\InventoryConfigOptionalFields;
use OSS\Model\InventoryConfigOssBucketDestination;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketInventoryTest extends TestOssClientBase
{
    public function testBucketInventory()
    {

        $id = "report2";
        $isEnabled = InventoryConfig::IS_ENABLED_TRUE;
        $filterPrefix = "filterPrefix/";
        $version = InventoryConfig::OBJECT_VERSION_ALL;
        $frequency = InventoryConfig::FREQUENCY_DAILY;

        $LastModifyBeginTimeStamp = "1637883649";
        $LastModifyEndTimeStamp = "1638347592";
        $LowerSizeBound = "1024";
        $UpperSizeBound = "1048576";
        $StorageClass = "Standard,IA";
        $configFilter = new InventoryConfigFilter($filterPrefix,$LastModifyBeginTimeStamp,$LastModifyEndTimeStamp,$LowerSizeBound,$UpperSizeBound,$StorageClass);
        $files = array(
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_SIZE),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_LAST_MODIFIED_DATE),

            new InventoryConfigOptionalFields(InventoryConfig::FIELD_ETAG),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_STORAGE_CLASS),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_IS_MULTIPART_UPLOADED),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_ENCRYPTION_STATUS),
        );

        $format = InventoryConfigOssBucketDestination::DEST_FORMAT;
        $accountId = getenv('OSS_ACCOUNT_ID');
        $roleArn = 'acs:ram::'.$accountId.':role/AliyunOSSRole';
        $bucketName = 'acs:oss:::'.$this->bucket;
        $prefix = 'prefix1';
        $configDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucketName,$prefix);

        $inventoryConfig = new InventoryConfig($id,$isEnabled,$frequency,$version,$configDestination,$configFilter,$files);
        try{
            $this->ossClient->putBucketInventory($this->bucket,$inventoryConfig);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try{
            Common::waitMetaSync();
            $result = $this->ossClient->getBucketInventory($this->bucket,"report2");
            var_dump($result);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try{
            Common::waitMetaSync();
            $result = $this->ossClient->listBucketInventory($this->bucket);
            var_dump($result);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try{
            Common::waitMetaSync();
            $result = $this->ossClient->deleteBucketInventory($this->bucket,'report2');
            var_dump($result);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

    }

}
