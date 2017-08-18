<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use \OSS\Model\RefererConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage ****************************************************************

//Sets referer whitelist
$refererConfig = new RefererConfig();
$refererConfig->setAllowEmptyReferer(true);
$refererConfig->addReferer("www.aliiyun.com");
$refererConfig->addReferer("www.aliiyuncs.com");
$ossClient->putBucketReferer($bucket, $refererConfig);
Common::println("bucket $bucket refererConfig created:" . $refererConfig->serializeToXml());
//Gets Referer white list
$refererConfig = $ossClient->getBucketReferer($bucket);
Common::println("bucket $bucket refererConfig fetched:" . $refererConfig->serializeToXml());

//Deletes referrer whitelist
$refererConfig = new RefererConfig();
$ossClient->putBucketReferer($bucket, $refererConfig);
Common::println("bucket $bucket refererConfig deleted");


//******************************* Below is the complete usage****************************************************

putBucketReferer($ossClient, $bucket);
getBucketReferer($ossClient, $bucket);
deleteBucketReferer($ossClient, $bucket);
getBucketReferer($ossClient, $bucket);

/**
 * Sets bucket referer config
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketReferer($ossClient, $bucket)
{
    $refererConfig = new RefererConfig();
    $refererConfig->setAllowEmptyReferer(true);
    $refererConfig->addReferer("www.aliiyun.com");
    $refererConfig->addReferer("www.aliiyuncs.com");
    try {
        $ossClient->putBucketReferer($bucket, $refererConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Gets bucket referrer config
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketReferer($ossClient, $bucket)
{
    $refererConfig = null;
    try {
        $refererConfig = $ossClient->getBucketReferer($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($refererConfig->serializeToXml() . "\n");
}

/**
 * Deletes the bucket referrer
 * Referer whitelist cannot be directly deleted. So use a empty one to overwrite it.
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketReferer($ossClient, $bucket)
{
    $refererConfig = new RefererConfig();
    try {
        $ossClient->putBucketReferer($bucket, $refererConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
