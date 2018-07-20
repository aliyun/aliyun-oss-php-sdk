<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//*******************************Simple Usage ***************************************************************

// Set bucket access logging rules. Access logs are stored under the same bucket with a 'access.log' prefix.
$obsClient->putBucketLogging($bucket, $bucket, "access.log", array());
Common::println("bucket $bucket lifecycleConfig created");

// Get bucket access logging rules
$loggingConfig = $obsClient->getBucketLogging($bucket, array());
Common::println("bucket $bucket lifecycleConfig fetched:" . $loggingConfig->serializeToXml());

// Delete bucket access logging rules
$loggingConfig = $obsClient->getBucketLogging($bucket, array());
Common::println("bucket $bucket lifecycleConfig deleted");

//******************************* For complete usage, see the following functions ****************************************************

putBucketLogging($obsClient, $bucket);
getBucketLogging($obsClient, $bucket);
deleteBucketLogging($obsClient, $bucket);
getBucketLogging($obsClient, $bucket);

/**
 * Set bucket logging configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketLogging($obsClient, $bucket)
{
    $option = array();
    // Access logs are stored in the same bucket.
    $targetBucket = $bucket;
    $targetPrefix = "access.log";

    try {
        $obsClient->putBucketLogging($bucket, $targetBucket, $targetPrefix, $option);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket logging configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketLogging($obsClient, $bucket)
{
    $loggingConfig = null;
    $options = array();
    try {
        $loggingConfig = $obsClient->getBucketLogging($bucket, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($loggingConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket logging configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketLogging($obsClient, $bucket)
{
    try {
        $obsClient->deleteBucketLogging($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
