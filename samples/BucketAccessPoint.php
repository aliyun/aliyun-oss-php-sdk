<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\AccessPointConfig;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// Put Bucket Access Point
$apName = "ap-name-01";
$networkOrigin = AccessPointConfig::VPC;
$vpcId = "vpc-123456789";
$accessConfig = new AccessPointConfig($apName,$networkOrigin,$vpcId);
$result = $ossClient->putBucketAccessPoint($bucket,$accessConfig);
printf("Put Access Point Result Access Point Arn:%s".PHP_EOL,$result->getAccessPointArn());
printf("Put Access Point Result Access Point Alias:%s".PHP_EOL,$result->getAlias());

// Get Bucket Access Point
$access = $ossClient->getBucketAccessPoint($bucket,$apName);
printf("Access Point Name:%s".PHP_EOL,$access->getAccessPointName());
printf("Access Point Bucket:%s".PHP_EOL,$access->getBucket());
printf("Access Point Account Id:%s".PHP_EOL,$access->getAccountId());
printf("Access Point Network Origin:%s".PHP_EOL,$access->getNetworkOrigin());
printf("Access Point Vpc Id:%s".PHP_EOL,$access->getVpcId());
printf("Access Point Arn:%s".PHP_EOL,$access->getAccessPointArn());
printf("Access Point Creation Date:%s".PHP_EOL,$access->getCreationDate());
printf("Access Point Alias:%s".PHP_EOL,$access->getAlias());
printf("Access Point Status:%s".PHP_EOL,$access->getStatus());
printf("Access Point Internal Endpoint:%s".PHP_EOL,$access->getInternalEndpoint());
printf("Access Point Public Endpoint:%s".PHP_EOL,$access->getPublicEndpoint());

// List Bucket Access Point
$list = $ossClient->listBucketAccessPoint($bucket);
printf("List Access Point Is Truncated:%s".PHP_EOL,$list->getIsTruncated());
printf("List Access Point Next Continuation Token:%s".PHP_EOL,$list->getNextContinuationToken());
printf("List Access Point Account Id:%s".PHP_EOL,$list->getAccountId());
$accessPoints = $list->getAccessPoints();
if (isset($accessPoints)){
    foreach ($accessPoints as $access){
        printf("Access Point Name:%s".PHP_EOL,$access->getAccessPointName());
        printf("Access Point Bucket:%s".PHP_EOL,$access->getBucket());
        printf("Access Point Network Origin:%s".PHP_EOL,$access->getNetworkOrigin());
        printf("Access Point Vpc Id:%s".PHP_EOL,$access->getVpcId());
        printf("Access Point Alias:%s".PHP_EOL,$access->getAlias());
        printf("Access Point Status:%s".PHP_EOL,$access->getStatus());
    }
}



// Delete Bucket Access Point
$apName = "ap-name-01";
$ossClient->deleteBucketAccessPoint($bucket,$apName);

// Put Access Point Policy
$policy = <<< BBBB
{
	"Version": "1",
	"Statement": [{
		"Effect": "Allow",
		"Action": [
			"oss:*"
		],
		"Principal": [
			"****8365787808****"
		],
		"Resource": [
			"acs:oss:ap-southeast-2:****92521021****:accesspoint/$apName",
			"acs:oss:ap-southeast-2:****92521021****:accesspoint/$apName/object/*"
		]
	}]
}
BBBB;

$ossClient->putAccessPointPolicy($bucket,$apName,$policy);

// Get Access Point Policy
$repPolicy = $ossClient->getAccessPointPolicy($bucket,$apName);
printf("Get Access Point Policy:%s".PHP_EOL,$repPolicy);

// Delete Access Point Policy
$ossClient->deleteAccessPointPolicy($bucket,$apName);

// Put Access Point Policy By Access Point Alias
$alias = $result->getAlias();
$ossClient->putAccessPointPolicy($alias,$apName,$policy);
// Get Access Point Policy By Access Point Alias
$result = $ossClient->getAccessPointPolicy($alias,$apName);
printf("Get Access Point Policy:%s".PHP_EOL,$result);

// Delete Access Point Policy By Access Point Alias
$ossClient->deleteAccessPointPolicy($alias,$apName);

//******************************* For complete usage, see the following functions ****************************************************

putBucketAccessPoint($ossClient, $bucket);
getBucketAccessPoint($ossClient, $bucket);
listBucketAccessPoint($ossClient, $bucket);
deleteBucketAccessPoint($ossClient, $bucket);
putAccessPointPolicy($ossClient, $bucket);
getAccessPointPolicy($ossClient, $bucket);
deleteAccessPointPolicy($ossClient, $bucket);
putAccessPointPolicyByAlias($ossClient, $alias);
getAccessPointPolicyByAlias($ossClient, $alias);
deleteAccessPointPolicyByAlias($ossClient, $alias);

/**
 * Put Bucket Access Point
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function putBucketAccessPoint($ossClient, $bucket)
{
	try {
        $apName = "ap-name-01";
        $networkOrigin = AccessPointConfig::VPC;
        $vpcId = "vpc-123456789";
        $accessConfig = new AccessPointConfig($apName,$networkOrigin,$vpcId);
        $result = $ossClient->putBucketAccessPoint($bucket,$accessConfig);
        printf("Put Access Point Result Access Point Arn:%s".PHP_EOL,$result->getAccessPointArn());
        printf("Put Access Point Result Access Point Alias:%s".PHP_EOL,$result->getAlias());
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get Bucket Access Point
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function getBucketAccessPoint($ossClient, $bucket)
{
	try {
        $apName = "ap-name-01";
        $access = $ossClient->getBucketAccessPoint($bucket,$apName);
        printf("Access Point Name:%s".PHP_EOL,$access->getAccessPointName());
        printf("Access Point Bucket:%s".PHP_EOL,$access->getBucket());
        printf("Access Point Account Id:%s".PHP_EOL,$access->getAccountId());
        printf("Access Point Network Origin:%s".PHP_EOL,$access->getNetworkOrigin());
        printf("Access Point Vpc Id:%s".PHP_EOL,$access->getVpcId());
        printf("Access Point Arn:%s".PHP_EOL,$access->getAccessPointArn());
        printf("Access Point Creation Date:%s".PHP_EOL,$access->getCreationDate());
        printf("Access Point Alias:%s".PHP_EOL,$access->getAlias());
        printf("Access Point Status:%s".PHP_EOL,$access->getStatus());
        printf("Access Point Internal Endpoint:%s".PHP_EOL,$access->getInternalEndpoint());
        printf("Access Point Public Endpoint:%s".PHP_EOL,$access->getPublicEndpoint());
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	
	print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * List Bucket Access Point
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function listBucketAccessPoint($ossClient, $bucket)
{
    try {
        $options = array();
        while (true) {
            $list = $ossClient->listBucketAccessPoint($bucket, $options);
            printf("List Access Point Is Truncated:%s" . PHP_EOL, var_export($list->getIsTruncated(), true));
            printf("List Access Point Next Continuation Token:%s" . PHP_EOL, $list->getNextContinuationToken());
            printf("List Access Point Account Id:%s" . PHP_EOL, $list->getAccountId());
            printf("List Access Point Max Keys:%s" . PHP_EOL, $list->getMaxKeys());
            $accessPoints = $list->getAccessPoints();
            if (isset($accessPoints)) {
                foreach ($accessPoints as $access) {
                    printf("Access Point Name:%s" . PHP_EOL, $access->getAccessPointName());
                    printf("Access Point Bucket:%s" . PHP_EOL, $access->getBucket());
                    printf("Access Point Network Origin:%s" . PHP_EOL, $access->getNetworkOrigin());
                    printf("Access Point Vpc Id:%s" . PHP_EOL, $access->getVpcId());
                    printf("Access Point Alias:%s" . PHP_EOL, $access->getAlias());
                    printf("Access Point Status:%s" . PHP_EOL, $access->getStatus());
                }
            }

            if ($list->getIsTruncated()) {
                $options[OssClient::OSS_CONTINUATION_TOKEN] = $list->getNextContinuationToken();
            } else {
                break;
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
 * Delete Bucket Access Point
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function deleteBucketAccessPoint($ossClient, $bucket)
{
    try{
        $apName = "ap-name-01";
        $ossClient->deleteBucketAccessPoint($bucket,$apName);
        printf("Delete Bucket Access Point Success!");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
    }
	print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Put Bucket Access Point
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function putAccessPointPolicy($ossClient, $bucket)
{
    try {
        $apName = "ap-name-01";
        $policy = <<< BBBB
{
	"Version": "1",
	"Statement": [{
		"Effect": "Allow",
		"Action": [
			"oss:*"
		],
		"Principal": [
			"****8365787808****"
		],
		"Resource": [
			"acs:oss:ap-southeast-2:****92521021****:accesspoint/$apName",
			"acs:oss:ap-southeast-2:****92521021****:accesspoint/$apName/object/*"
		]
	}]
}
BBBB;

        $ossClient->putAccessPointPolicy($bucket,$apName,$policy);
        printf("Put Access Point Success!");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get Access Point Policy
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function getAccessPointPolicy($ossClient, $bucket)
{
    try {
        $apName = "ap-name-01";
        $result = $ossClient->getAccessPointPolicy($bucket,$apName);
        printf("Get Access Point Policy:%s".PHP_EOL,$result);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Delete Access Point Policy
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws RequestCore_Exception
 */
function deleteAccessPointPolicy($ossClient, $bucket)
{
    try{
        $apName = "ap-name-01";
        $ossClient->deleteAccessPointPolicy($bucket,$apName);
        printf("Delete Access Point Policy Success!");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Put Bucket Access Point By Alias
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $alias access point alias name
 * @return null
 * @throws RequestCore_Exception
 */
function putAccessPointPolicyByAlias($ossClient, $alias)
{
    try {
        $apName = "ap-name-01";
        $policy = <<< BBBB
{
	"Version": "1",
	"Statement": [{
		"Effect": "Allow",
		"Action": [
			"oss:*"
		],
		"Principal": [
			"****8365787808****"
		],
		"Resource": [
			"acs:oss:ap-southeast-2:****92521021****:accesspoint/$apName",
			"acs:oss:ap-southeast-2:****92521021****:accesspoint/$apName/object/*"
		]
	}]
}
BBBB;

        $ossClient->putAccessPointPolicy($alias,$apName,$policy);
        printf("Put Access Point Success!");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get Access Point Policy By Alias
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $alias access point alias name
 * @return null
 * @throws RequestCore_Exception
 */
function getAccessPointPolicyByAlias($ossClient, $alias)
{
    try {
        $apName = "ap-name-01";
        $result = $ossClient->getAccessPointPolicy($alias,$apName);
        printf("Get Access Point Policy:%s".PHP_EOL,$result);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Delete Access Point Policy By Alias
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $alias access point alias name
 * @return null
 * @throws RequestCore_Exception
 */
function deleteAccessPointPolicyByAlias($ossClient, $alias)
{
    try{
        $apName = "ap-name-01";
        $ossClient->deleteAccessPointPolicy($alias,$apName);
        printf("Delete Access Point Policy Success!");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
