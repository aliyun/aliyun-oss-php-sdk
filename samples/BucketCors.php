<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;
use OBS\Model\CorsConfig;
use OBS\Model\CorsRule;

$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);
$bucket = Common::getBucketName();


//******************************* Simple usage****************************************************************

// Set cors configuration
$corsConfig = new CorsConfig();
$rule = new CorsRule();
$rule->addAllowedHeader("x-obs-header");
$rule->addAllowedOrigin("http://www.b.com");
$rule->addAllowedMethod("POST");
$rule->setMaxAgeSeconds(10);
$corsConfig->addRule($rule);
$obsClient->putBucketCors($bucket, $corsConfig);
Common::println("bucket $bucket corsConfig created:" . $corsConfig->serializeToXml());

// Get cors configuration
$corsConfig = $obsClient->getBucketCors($bucket);
Common::println("bucket $bucket corsConfig fetched:" . $corsConfig->serializeToXml());

// Delete cors configuration
$obsClient->deleteBucketCors($bucket);
Common::println("bucket $bucket corsConfig deleted");

//******************************* For complete usage, see the following functions  *****************************************************

putBucketCors($obsClient, $bucket);
getBucketCors($obsClient, $bucket);
deleteBucketCors($obsClient, $bucket);
getBucketCors($obsClient, $bucket);

/**
 * Set bucket cores
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketCors($obsClient, $bucket)
{
    $corsConfig = new CorsConfig();
    $rule = new CorsRule();
    $rule->addAllowedHeader("x-obs-header");
    $rule->addAllowedOrigin("http://www.b.com");
    $rule->addAllowedMethod("POST");
    $rule->setMaxAgeSeconds(10);
    $corsConfig->addRule($rule);

    try {
        $obsClient->putBucketCors($bucket, $corsConfig);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get and print the cors configuration of a bucket
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketCors($obsClient, $bucket)
{
    $corsConfig = null;
    try {
        $corsConfig = $obsClient->getBucketCors($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($corsConfig->serializeToXml() . "\n");
}

/**
 * Delete all cors configuraiton of a bucket
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketCors($obsClient, $bucket)
{
    try {
        $obsClient->deleteBucketCors($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

