<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\LifecycleAction;
use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleRule;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************

// Set lifecycle configuration
$lifecycleConfig = new LifecycleConfig();
$actions = array();
$actions[] = new LifecycleAction("Expiration", "Days", 3);
$lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
$lifecycleConfig->addRule($lifecycleRule);
$ossClient->putBucketLifecycle($bucket, $lifecycleConfig);
Common::println("bucket $bucket lifecycleConfig created:" . $lifecycleConfig->serializeToXml());

// Set lifecycle configuration (many tags)
$lifecycleConfig = new LifecycleConfig();
$actions = array();
$actions[] = new LifecycleAction("Expiration", "Days", 3);
$actions[] = new LifecycleAction("Tag", "Key", "key1");
$actions[] = new LifecycleAction("Tag", "Value", "value1");

$actions[] = new LifecycleAction("Tag", "Key", "key2");
$actions[] = new LifecycleAction("Tag", "Value", "value2");
$lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);

$lifecycleConfig->addRule($lifecycleRule);
$ossClient->putBucketLifecycle($bucket, $lifecycleConfig);
Common::println("bucket $bucket lifecycleConfig created:" . $lifecycleConfig->serializeToXml());

// Get lifecycle configuration
$lifecycleConfig = $ossClient->getBucketLifecycle($bucket);
Common::println("bucket $bucket lifecycleConfig fetched:" . $lifecycleConfig->serializeToXml());

// Delete bucket lifecycle configuration
$ossClient->deleteBucketLifecycle($bucket);
Common::println("bucket $bucket lifecycleConfig deleted");


//***************************** For complete usage, see the following functions  ***********************************************

putBucketLifecycle($ossClient, $bucket);
getBucketLifecycle($ossClient, $bucket);
deleteBucketLifecycle($ossClient, $bucket);

/**
 * Set bucket lifecycle configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketLifecycle($ossClient, $bucket)
{
    $lifecycleConfig = new LifecycleConfig();
    $actions = array();
    $actions[] = new LifecycleAction(OssClient::OSS_LIFECYCLE_EXPIRATION, OssClient::OSS_LIFECYCLE_TIMING_DAYS, 3);
    $lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
    $lifecycleConfig->addRule($lifecycleRule);
    $actions = array();
    $actions[] = new LifecycleAction(OssClient::OSS_LIFECYCLE_EXPIRATION, OssClient::OSS_LIFECYCLE_TIMING_DATE, '2022-10-12T00:00:00.000Z');
    $lifecycleRule = new LifecycleRule("delete temporary files", "temporary/", "Enabled", $actions);
    $lifecycleConfig->addRule($lifecycleRule);
    try {
        $ossClient->putBucketLifecycle($bucket, $lifecycleConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket lifecycle configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketLifecycle($ossClient, $bucket)
{
    try {
        $lifecycleConfig = $ossClient->getBucketLifecycle($bucket);
		foreach ($lifecycleConfig->getRules() as $key => $info){
			printf("=====================================".PHP_EOL);
			// 查看规则id。
			printf("rule id:".$info->getId().PHP_EOL);
			// 查看规则状态。
			printf("rule status:".$info->getStatus().PHP_EOL);
			// 查看规则前缀。。
			printf("rule prefix:".$info->getPrefix().PHP_EOL);
			// 查看规则标签。
			if($info->hasTags()){
				printf("rule tagging:".$info->getTags().PHP_EOL);
			}
			// 查看过期天数规则。
			if ($info->hasExpirationDays()) {
				printf("rule expiration days: ".$info->getExpirationDays().PHP_EOL);
			}
			// 查看过期日期规则。
			if ($info->hasCreatedBeforeDate()) {
				printf("rule expiration create before days: ".$info->getCreatedBeforeDate().PHP_EOL);
			}
			// 查看过期分片规则。
			if($info->hasAbortMultipartUpload()) {
				if($info->hasAbortMultipartUploadExpirationDays()) {
					printf("rule abort uppart days: " . $info->getAbortMultipartUploadExpirationDays().PHP_EOL);
				}
			
				if ( $info->hasAbortMultipartUploadCreatedBeforeDate()) {
					printf("rule abort uppart create before date: " . $info->getAbortMultipartUploadCreatedBeforeDate().PHP_EOL);
				}
			}
			// 查看存储类型转换规则。
			if ($info->hasStorageTransition()) {
				if ($info->hasStorageTransitionExpirationDays()) {
					printf("rule storage trans days: " . $info->getStorageTransitionExpirationDays() .
						" trans storage class: " . $info->getStorageTransitionStorageClass().PHP_EOL);
				}
			
				if ($info->hasStorageTransitionCreatedBeforeDate()) {
					printf("rule storage trans before create date: " . $info->getStorageTransitionCreatedBeforeDate().PHP_EOL);
				}
			}
		
			// 查看是否自动删除过期删除标记。
			if ($info->hasExpiredDeleteMarker()) {
				printf("rule expired delete marker: " .$info->getExpiredDeleteMarker());
			}
			// 查看非当前版本Object存储类型转换规则。
			if ($info->hasNoncurrentVersionStorageTransitions()) {
				printf("rule noncurrent versions trans days:" .$info->getNoncurrentVersionStorageTransitionsNoncurrentDays() .
					" trans storage class: " . $info->getNoncurrentVersionStorageTransitionsStorageClass().PHP_EOL);
			}
			// 查看非当前版本Object过期规则。
			if ($info->hasNoncurrentVersionExpiration()) {
				printf("rule noncurrent versions expiration days:" . $info->getNoncurrentVersionExpirationDays().PHP_EOL);
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
 * Delete bucket lifecycle configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketLifecycle($ossClient, $bucket)
{
    try {
        $ossClient->deleteBucketLifecycle($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


