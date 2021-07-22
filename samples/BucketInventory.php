<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\InventoryConfig;
use OSS\Model\InventoryOssBucketDestination;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************

// Set inventory configuration

$inventoryConfig = new InventoryConfig();
// 设置清单配置id。
$inventoryConfig->addId('report2');
// 清单配置是否启用的标识, true或false。
$inventoryConfig->addIsEnabled(InventoryConfig::IS_ENABIED_TRUE);
// 设置清单筛选规则，指定筛选object的前缀。
$inventoryConfig->addPrefix('filterPrefix');
// 设置清单中包含的object的版本为当前版本。如果设置为InventoryIncludedObjectVersions.All则表示object的所有版本，在版本控制状态下生效。
$inventoryConfig->addIncludedObjectVersions(InventoryConfig::OBJECT_VERSION_ALL);
// 设置清单的生成计划，以下示例为每周一次。其中，Weekly对应每周一次，Daily对应每天一次。
$inventoryConfig->addSchedule(InventoryConfig::FREQUENCY_DAILY);
// 设置清单中包含的fields属性。
$fields = array(
    InventoryConfig::FIELD_SIZE,
    InventoryConfig::FIELD_LAST_MODIFIED_DATE,
    InventoryConfig::FIELD_IS_MULTIPART_UPLOADED,
    InventoryConfig::FIELD_ETAG,
    InventoryConfig::FIELD_STORAGECLASS,
    InventoryConfig::FIELD_ENCRYPTIONSTATUS,
);
$inventoryConfig->addOptionalFields($fields);
// 创建清单的bucket目的地配置。
$ossBucketDestination = new InventoryOssBucketDestination();
$ossBucketDestination->addFormat(InventoryOssBucketDestination::DEST_FORMAT);
$ossBucketDestination->addAccountId('<your_account_id>');
$ossBucketDestination->addRoleArn('<your_account_rolearn>');
$ossBucketDestination->addBucketName('<your_bucket_name>');
$ossBucketDestination->addPrefix('prefix');
// 如果需要使用KMS加密清单，请参考如下设置
//$ossBucketDestination->addEncryptionKms('key1');
//如果需要使用OSS服务端加密清单，请参考如下设置。
//$ossBucketDestination->addEncryptionOss();
$inventoryConfig->addDestination($ossBucketDestination);
$ossClient->putBucketInventory($bucket,$inventoryConfig);
Common::println("bucket $bucket Inventory created:" . $inventoryConfig->serializeToXml());


// Get inventory configuration

$inventoryConfigId = 'report2';
$result = $ossClient->getBucketInventory($bucket,$inventoryConfigId);
Common::println("===Inventory configuration===");
Common::println("inventoryId: ".$result->getId());
Common::println("isenabled: ".$result->getIsEnabled());
Common::println("includedVersions: ".$result->getIncludedObjectVersions());
Common::println("schdule: ".$result->getSchedule()['Frequency']);
if ($result->getFilter()['Prefix']) {
    Common::println("filter, prefix: ".$result->getFilter()['Prefix']);
}

if($result->getOptionalFields()['Field']){
    foreach ($result->getOptionalFields()['Field'] as $field){
        Common::println("field: ".$field);
    }
}
Common::println("===bucket destination config===");
$destination = $result->getOssBucketDestination();
Common::println("format: ".$destination['Format']);
Common::println("bucket: ".$destination['Bucket']);
Common::println("prefix: ".$destination['Prefix']);
Common::println("accountId: ".$destination['AccountId']);
Common::println("roleArn: ".$destination['RoleArn']);
if($destination['Encryption']){
    if(isset($destination['Encryption']['SSE-KMS'])){
        Common::println("server-side kms encryption key id: ".$destination['Encryption']['SSE-KMS']['KeyId']);
    }

    if(isset($destination['Encryption']['SSE-OSS'])){
        Common::println("server-side oss encryption.");
    }

}


// list inventory configuration
$option = array(
    OssClient::OSS_CONTINUATION_TOKEN => null
);
$bool = true;
while ($bool){
    $result = $ossClient->listBucketInventory($bucket,$option);
    Common::println("=======List bucket inventory configuration=======");
    Common::println("istruncated: {$result->getIsTruncated()}");
    Common::println("nextContinuationToken: {$result->getNextContinuationToken()}");
    ## 查看列举Object的版本信息。
    foreach ($result->getInventoryList() as $key => $info){
        Common::println("===Inventory configuration===");
        Common::println("inventoryId: {$info->getId()}");
        Common::println("isenabled: {$info->getIsEnabled()}");
        Common::println("includedVersions: {$info->getIncludedObjectVersions()}");
        Common::println("schdule: {$info->getSchedule()['Frequency']}");
        if ($info->getFilter()['Prefix']) {
            Common::println("filter, prefix: {$info->getFilter()['Prefix']}");
        }

        if($info->getOptionalFields()['Field']){
            foreach ($info->getOptionalFields()['Field'] as $field){
                Common::println("field: {$field}");
            }
        }
        Common::println("===bucket destination config===");
        $destination = $info->getOssBucketDestination();
        Common::println("format: {$destination['Format']}");
        Common::println("bucket: {$destination['Bucket']}");
        Common::println("prefix: {$destination['Prefix']}");
        Common::println("accountId: {$destination['AccountId']}");
        Common::println("roleArn: {$destination['RoleArn']}");
        if($destination['Encryption']){
            if(isset($destination['Encryption']['SSE-KMS'])){
                Common::println("server-side kms encryption key id: {$destination['Encryption']['SSE-KMS']['KeyId']}");
            }

            if(isset($destination['Encryption']['SSE-OSS'])){
                Common::println("server-side oss encryption.");
            }

        }
    }

    if($result->getIsTruncated() === 'true'){
        $option[OssClient::OSS_CONTINUATION_TOKEN] = $result->getNextContinuationToken();
    }else{
        $bool = false;
    }
}

// delete inventory configuration
$inventoryConfigId = 'report2';
$ossClient->deleteBucketInventory($bucket,$inventoryConfigId);
printf('delete Inventory %s Success'. "\n",$inventoryConfigId);
