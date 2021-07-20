<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\InventoryConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************

// Set inventory configuration

$config = array(
    'Id'=>'report2',
    'IsEnabled'=>true,
    'Destination'=> array(
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
        ),
    ),
    'Schedule'=>array(
        'Frequency'=>'Daily',
    ),
    'IncludedObjectVersions'=>'All',
    'OptionalFields'=>array(
        'Field'=>array('Size','LastModifiedDate')
    )
);
$ossClient->putBucketInventory($bucket,$config);
$inventoryConfig = new InventoryConfig();
$inventoryConfig->setConfigs($config);
Common::println("bucket $bucket Inventory created:" . $inventoryConfig->serializeToXml());

// Get inventory configuration

$inventoryConfigId = 'report2';
$result = $ossClient->getBucketInventory($bucket,$inventoryConfigId);
$inventoryConfig = new InventoryConfig();
$inventoryConfig->setConfigs($result);
Common::println("bucket $bucket Inventory fetched:" . $inventoryConfig->serializeToXml());


// list inventory configuration
$result = $ossClient->listBucketInventory($bucket);
Common::println("bucket $bucket Inventory fetched:" . ($result));

// delete inventory configuration
$inventoryConfigId = 'report2';
$ossClient->deleteBucketInventory($bucket,$inventoryConfigId);
printf('delete Inventory %s Success'. "\n",$id);
