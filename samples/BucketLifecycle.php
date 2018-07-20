<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;
use OBS\Model\LifecycleAction;
use OBS\Model\LifecycleConfig;
use OBS\Model\LifecycleRule;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//******************************* Simple Usage *******************************************************

// Set lifecycle configuration
$lifecycleConfig = new LifecycleConfig();
$actions = array();
$actions[] = new LifecycleAction("Expiration", "Days", 3);
$lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
$lifecycleConfig->addRule($lifecycleRule);
$obsClient->putBucketLifecycle($bucket, $lifecycleConfig);
Common::println("bucket $bucket lifecycleConfig created:" . $lifecycleConfig->serializeToXml());

// Get lifecycle configuration
$lifecycleConfig = $obsClient->getBucketLifecycle($bucket);
Common::println("bucket $bucket lifecycleConfig fetched:" . $lifecycleConfig->serializeToXml());

// Delete bucket lifecycle configuration
$obsClient->deleteBucketLifecycle($bucket);
Common::println("bucket $bucket lifecycleConfig deleted");


//***************************** For complete usage, see the following functions  ***********************************************

putBucketLifecycle($obsClient, $bucket);
getBucketLifecycle($obsClient, $bucket);
deleteBucketLifecycle($obsClient, $bucket);
getBucketLifecycle($obsClient, $bucket);

/**
 * Set bucket lifecycle configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketLifecycle($obsClient, $bucket)
{
    $lifecycleConfig = new LifecycleConfig();
    $actions = array();
    $actions[] = new LifecycleAction(ObsClient::OBS_LIFECYCLE_EXPIRATION, ObsClient::OBS_LIFECYCLE_TIMING_DAYS, 3);
    $lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
    $lifecycleConfig->addRule($lifecycleRule);
    $actions = array();
    $actions[] = new LifecycleAction(ObsClient::OBS_LIFECYCLE_EXPIRATION, ObsClient::OBS_LIFECYCLE_TIMING_DATE, '2022-10-12T00:00:00.000Z');
    $lifecycleRule = new LifecycleRule("delete temporary files", "temporary/", "Enabled", $actions);
    $lifecycleConfig->addRule($lifecycleRule);
    try {
        $obsClient->putBucketLifecycle($bucket, $lifecycleConfig);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket lifecycle configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketLifecycle($obsClient, $bucket)
{
    $lifecycleConfig = null;
    try {
        $lifecycleConfig = $obsClient->getBucketLifecycle($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($lifecycleConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket lifecycle configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketLifecycle($obsClient, $bucket)
{
    try {
        $obsClient->deleteBucketLifecycle($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


