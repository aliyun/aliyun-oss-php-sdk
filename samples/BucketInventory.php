<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\Model\InventoryConfigFilter;
use OSS\Model\InventoryConfigOptionalFields;
use OSS\OssClient;
use OSS\Model\InventoryConfig;
use OSS\Model\InventoryConfigOssBucketDestination;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************


// Set the manifest configuration ID
$id = "report2";
// The identification of whether the manifest configuration is enabled, true or false.
$isEnabled = InventoryConfig::IS_ENABLED_TRUE;
// Set list filtering rules and specify the prefix of filtering objects.
$prefix = "filterPrefix";
// Set the version of the object contained in the list to the current version. If set to inventoryincludedobjectversions All indicates all versions of the object, which take effect in version control status
$version = InventoryConfig::OBJECT_VERSION_ALL;
// Set the generation plan of the list. The following example is once a week. Weekly corresponds to once a week and daily corresponds to once a day.
$frequency = InventoryConfig::FREQUENCY_DAILY;

$configFilter = new InventoryConfigFilter($prefix);
// Set the fields property contained in the manifest.
$files = array(
    new InventoryConfigOptionalFields(InventoryConfig::FIELD_SIZE),
    new InventoryConfigOptionalFields(InventoryConfig::FIELD_LAST_MODIFIED_DATE),
    new InventoryConfigOptionalFields(InventoryConfig::FIELD_IS_MULTIPART_UPLOADED),
    new InventoryConfigOptionalFields(InventoryConfig::FIELD_ETAG),
    new InventoryConfigOptionalFields(InventoryConfig::FIELD_STORAGE_CLASS),
    new InventoryConfigOptionalFields(InventoryConfig::FIELD_ENCRYPTION_STATUS),
);

// Create the bucket destination configuration of the manifest.

$format = InventoryConfigOssBucketDestination::DEST_FORMAT;
$accountId = '<your_account_id>';
$roleArn = '<your_account_rolearn>';
$bucketName = 'acs:oss:::destbucket';
$prefix = 'prefix';
$configDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucketName,$prefix);
// If you need to use kms encryption list, please refer to the following settings
//$kmsId = "kms_key_id";
//$ossBucketDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucketName,$prefix,null,$kmsId);
// If you need to use the OSS server encryption list, please refer to the following settings
//$ossId = "oss_key_id";
//$ossBucketDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucketName,$prefix,$ossId);

// Set inventory configuration
$inventoryConfig = new InventoryConfig($id,$isEnabled,$frequency,$version,$configDestination,$configFilter,$files);
$ossClient->putBucketInventory($bucket,$inventoryConfig);
Common::println("bucket $bucket Inventory created:" . $inventoryConfig->serializeToXml());


// Get inventory configuration

$inventoryConfigId = 'report2';
$result = $ossClient->getBucketInventory($bucket,$inventoryConfigId);
Common::println("===Inventory configuration===");
Common::println("Inventory Id: ".$result->getId());
Common::println("Is Enabled: ".$result->getIsEnabled());
Common::println("Included Versions: ".$result->getIncludedObjectVersions());
Common::println("Schedule Frequency: ".$result->getSchedule());

if ($result->getFilter()){
    Common::println("Filter Prefix: ".$result->getFilter()->getPrefix());
    Common::println("Filter Last Modify Begin Time Stamp: ".$result->getFilter()->getLastModifyBeginTimeStamp());
    Common::println("Filter Last Modify End Time Stamp: ".$result->getFilter()->getLastModifyEndTimeStamp());
    Common::println("Filter Lower Size Bound: ".$result->getFilter()->getLowerSizeBound());
    Common::println("Filter Upper Size Bound: ".$result->getFilter()->getUpperSizeBound());
    Common::println("Filter Storage Class: ".$result->getFilter()->getStorageClass());
}

if ($result->getOptionalFields()){
    foreach ($result->getOptionalFields() as $field){
        Common::println("Optional Fields Filed: ".$field->getFiled());
    }
}
Common::println("===bucket destination config===");

if ($result->getDestination()){
    Common::println("OSS Bucket Destination Format: ".$result->getDestination()->getFormat());
    Common::println("OSS Bucket Destination AccountId: ".$result->getDestination()->getAccountId());
    Common::println("OSS Bucket Destination Role Arn: ".$result->getDestination()->getRoleArn());
    Common::println("OSS Bucket Destination Bucket: ".$result->getDestination()->getBucket());
    Common::println("OSS Bucket Destination Prefix: ".$result->getDestination()->getPrefix());

    if ($result->getDestination()->getOssId()){
        Common::println("Server Side Encryption OSS Key Id: ".$result->getDestination()->getOssId());
    }
    if ($result->getDestination()->getKmsId()){
        Common::println("Server Side Encryption Kms Key Id: ".$result->getDestination()->getKmsId());
    }
}


// list inventory configuration

$option = array(
    OssClient::OSS_CONTINUATION_TOKEN => null
);
$bool = true;
while ($bool) {
    $list = $ossClient->listBucketInventory($bucket);
    Common::println("=======List bucket inventory configuration=======");
    Common::println("Is Truncated: " . $list->getIsTruncated());
    Common::println("Next Continuation Token: " . $list->getNextContinuationToken());
    foreach ($list->getInventoryList() as $key => $result) {
        Common::println("Inventory Id: " . $result->getId());
        Common::println("Is Enabled: " . $result->getIsEnabled());
        Common::println("Included Versions: " . $result->getIncludedObjectVersions());
        Common::println("Schedule Frequency: " . $result->getSchedule());

        if ($result->getFilter()) {
            Common::println("Filter Prefix: " . $result->getFilter()->getPrefix());
            Common::println("Filter Last Modify Begin Time Stamp: " . $result->getFilter()->getLastModifyBeginTimeStamp());
            Common::println("Filter Last Modify End Time Stamp: " . $result->getFilter()->getLastModifyEndTimeStamp());
            Common::println("Filter Lower Size Bound: " . $result->getFilter()->getLowerSizeBound());
            Common::println("Filter Upper Size Bound: " . $result->getFilter()->getUpperSizeBound());
            Common::println("Filter Storage Class: " . $result->getFilter()->getStorageClass());
        }

        if ($result->getOptionalFields()) {
            foreach ($result->getOptionalFields() as $field) {
                Common::println("Optional Fields Filed: " . $field->getFiled());
            }
        }
        Common::println("===bucket destination config===");

        if ($result->getDestination()) {
            Common::println("OSS Bucket Destination Format: " . $result->getDestination()->getFormat());
            Common::println("OSS Bucket Destination AccountId: " . $result->getDestination()->getAccountId());
            Common::println("OSS Bucket Destination Role Arn: " . $result->getDestination()->getRoleArn());
            Common::println("OSS Bucket Destination Bucket: " . $result->getDestination()->getBucket());
            Common::println("OSS Bucket Destination Prefix: " . $result->getDestination()->getPrefix());

            if ($result->getDestination()->getOssId()) {
                Common::println("Server Side Encryption OSS Key Id: " . $result->getDestination()->getOssId());
            }
            if ($result->getDestination()->getKmsId()) {
                Common::println("Server Side Encryption Kms Key Id: " . $result->getDestination()->getKmsId());
            }
        }
    }
    if ($list->getIsTruncated() === 'true') {
        $option[OssClient::OSS_CONTINUATION_TOKEN] = $list->getNextContinuationToken();
    } else {
        $bool = false;
    }
}

// delete inventory configuration
$inventoryConfigId = 'report2';
$ossClient->deleteBucketInventory($bucket,$inventoryConfigId);
printf('delete Inventory %s Success'. "\n",$inventoryConfigId);


putBucketInventory($ossClient,$bucket);
getBucketInventory($ossClient,$bucket);
listBucketInventory($ossClient,$bucket);
deleteBucketInventory($ossClient,$bucket);


/**
 * put bucket inventory configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketInventory($ossClient,$bucket){
    try {
        $id = "report2";
        $isEnabled = InventoryConfig::IS_ENABLED_TRUE;
        $filterPrefix = "filterPrefix";
        $version = InventoryConfig::OBJECT_VERSION_ALL;
        $frequency = InventoryConfig::FREQUENCY_DAILY;
        $configFilter = new InventoryConfigFilter($filterPrefix);
        $files = array(
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_SIZE),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_LAST_MODIFIED_DATE),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_IS_MULTIPART_UPLOADED),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_ETAG),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_STORAGE_CLASS),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_ENCRYPTION_STATUS),
        );

        $format = InventoryConfigOssBucketDestination::DEST_FORMAT;
        $accountId = '<your_account_id>';
        $roleArn = '<your_account_rolearn>';
        $bucketName = 'acs:oss:::destbucket';
        $prefix = 'prefix';
        $configDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucketName,$prefix);
        $inventoryConfig = new InventoryConfig($id,$isEnabled,$frequency,$version,$configDestination,$configFilter,$files);
        $ossClient->putBucketInventory($bucket,$inventoryConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * get bucket inventory configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getBucketInventory($ossClient,$bucket){
    $inventoryConfigId = 'report2';
    try {
        $result = $ossClient->getBucketInventory($bucket,$inventoryConfigId);
        Common::println("===Inventory configuration===");
        Common::println("Inventory Id: ".$result->getId());
        Common::println("Is Enabled: ".$result->getIsEnabled());
        Common::println("Included Versions: ".$result->getIncludedObjectVersions());
        Common::println("Schedule Frequency: ".$result->getSchedule());

        if ($result->getFilter()){
            Common::println("Filter Prefix: ".$result->getFilter()->getPrefix());
            Common::println("Filter Last Modify Begin Time Stamp: ".$result->getFilter()->getLastModifyBeginTimeStamp());
            Common::println("Filter Last Modify End Time Stamp: ".$result->getFilter()->getLastModifyEndTimeStamp());
            Common::println("Filter Lower Size Bound: ".$result->getFilter()->getLowerSizeBound());
            Common::println("Filter Upper Size Bound: ".$result->getFilter()->getUpperSizeBound());
            Common::println("Filter Storage Class: ".$result->getFilter()->getStorageClass());
        }

        if ($result->getOptionalFields()){
            foreach ($result->getOptionalFields() as $field){
                Common::println("Optional Fields Filed: ".$field->getFiled());
            }
        }
        Common::println("===bucket destination config===");

        if ($result->getDestination()){
            Common::println("OSS Bucket Destination Format: ".$result->getDestination()->getFormat());
            Common::println("OSS Bucket Destination AccountId: ".$result->getDestination()->getAccountId());
            Common::println("OSS Bucket Destination Role Arn: ".$result->getDestination()->getRoleArn());
            Common::println("OSS Bucket Destination Bucket: ".$result->getDestination()->getBucket());
            Common::println("OSS Bucket Destination Prefix: ".$result->getDestination()->getPrefix());

            if ($result->getDestination()->getOssId()){
                Common::println("Server Side Encryption OSS Key Id: ".$result->getDestination()->getOssId());
            }
            if ($result->getDestination()->getKmsId()){
                Common::println("Server Side Encryption Kms Key Id: ".$result->getDestination()->getKmsId());
            }
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * list bucket inventory configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function listBucketInventory($ossClient,$bucket){
    try {
        $option = array(
            OssClient::OSS_CONTINUATION_TOKEN => null
        );
        $bool = true;
        while ($bool) {
            $list = $ossClient->listBucketInventory($bucket, $option);
            Common::println("=======List bucket inventory configuration=======");
            Common::println("Is Truncated: " . $list->getIsTruncated());
            Common::println("Next Continuation Token: " . $list->getNextContinuationToken());
            foreach ($list->getInventoryList() as $key => $result) {
                Common::println("Inventory Id: " . $result->getId());
                Common::println("Is Enabled: " . $result->getIsEnabled());
                Common::println("Included Versions: " . $result->getIncludedObjectVersions());
                Common::println("Schedule Frequency: " . $result->getSchedule());

                if ($result->getFilter()) {
                    Common::println("Filter Prefix: " . $result->getFilter()->getPrefix());
                    Common::println("Filter Last Modify Begin Time Stamp: " . $result->getFilter()->getLastModifyBeginTimeStamp());
                    Common::println("Filter Last Modify End Time Stamp: " . $result->getFilter()->getLastModifyEndTimeStamp());
                    Common::println("Filter Lower Size Bound: " . $result->getFilter()->getLowerSizeBound());
                    Common::println("Filter Upper Size Bound: " . $result->getFilter()->getUpperSizeBound());
                    Common::println("Filter Storage Class: " . $result->getFilter()->getStorageClass());
                }

                if ($result->getOptionalFields()) {
                    foreach ($result->getOptionalFields() as $field) {
                        Common::println("Optional Fields Filed: " . $field->getFiled());
                    }
                }
                Common::println("===bucket destination config===");

                if ($result->getDestination()) {
                    Common::println("OSS Bucket Destination Format: " . $result->getDestination()->getFormat());
                    Common::println("OSS Bucket Destination AccountId: " . $result->getDestination()->getAccountId());
                    Common::println("OSS Bucket Destination Role Arn: " . $result->getDestination()->getRoleArn());
                    Common::println("OSS Bucket Destination Bucket: " . $result->getDestination()->getBucket());
                    Common::println("OSS Bucket Destination Prefix: " . $result->getDestination()->getPrefix());

                    if ($result->getDestination()->getOssId()) {
                        Common::println("Server Side Encryption OSS Key Id: " . $result->getDestination()->getOssId());
                    }
                    if ($result->getDestination()->getKmsId()) {
                        Common::println("Server Side Encryption Kms Key Id: " . $result->getDestination()->getKmsId());
                    }
                }
            }

            if ($list->getIsTruncated() === 'true') {
                $option[OssClient::OSS_CONTINUATION_TOKEN] = $list->getNextContinuationToken();
            } else {
                $bool = false;
            }
        }

    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * delete bucket inventory configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketInventory($ossClient,$bucket){
    $inventoryConfigId = 'report2';
    try {
        $ossClient->deleteBucketInventory($bucket,$inventoryConfigId);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}