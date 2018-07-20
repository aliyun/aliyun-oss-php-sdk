<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;
use \OBS\Model\RefererConfig;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//******************************* Simple Usage ****************************************************************

// Set referer whitelist
$refererConfig = new RefererConfig();
$refererConfig->setAllowEmptyReferer(true);
$refererConfig->addReferer("www.aliiyun.com");
$refererConfig->addReferer("www.aliiyuncs.com");
$obsClient->putBucketReferer($bucket, $refererConfig);
Common::println("bucket $bucket refererConfig created:" . $refererConfig->serializeToXml());
// Get referer whitelist
$refererConfig = $obsClient->getBucketReferer($bucket);
Common::println("bucket $bucket refererConfig fetched:" . $refererConfig->serializeToXml());

// Delete referrer whitelist
$refererConfig = new RefererConfig();
$obsClient->putBucketReferer($bucket, $refererConfig);
Common::println("bucket $bucket refererConfig deleted");


//******************************* For complete usage, see the following functions ****************************************************

putBucketReferer($obsClient, $bucket);
getBucketReferer($obsClient, $bucket);
deleteBucketReferer($obsClient, $bucket);
getBucketReferer($obsClient, $bucket);

/**
 * Set bucket referer configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketReferer($obsClient, $bucket)
{
    $refererConfig = new RefererConfig();
    $refererConfig->setAllowEmptyReferer(true);
    $refererConfig->addReferer("www.aliiyun.com");
    $refererConfig->addReferer("www.aliiyuncs.com");
    try {
        $obsClient->putBucketReferer($bucket, $refererConfig);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket referer configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketReferer($obsClient, $bucket)
{
    $refererConfig = null;
    try {
        $refererConfig = $obsClient->getBucketReferer($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($refererConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket referer configuration
 * Referer whitelist cannot be directly deleted. So use a empty one to overwrite it.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketReferer($obsClient, $bucket)
{
    $refererConfig = new RefererConfig();
    try {
        $obsClient->putBucketReferer($bucket, $refererConfig);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
