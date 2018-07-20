<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;
use OBS\Model\WebsiteConfig;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//******************************* Simple Usage ***************************************************************

// Set bucket static website configuration
$websiteConfig = new WebsiteConfig("index.html", "error.html");
$obsClient->putBucketWebsite($bucket, $websiteConfig);
Common::println("bucket $bucket websiteConfig created:" . $websiteConfig->serializeToXml());

// Get bucket static website configuration
$websiteConfig = $obsClient->getBucketWebsite($bucket);
Common::println("bucket $bucket websiteConfig fetched:" . $websiteConfig->serializeToXml());

// Delete bucket static website configuration
$obsClient->deleteBucketWebsite($bucket);
Common::println("bucket $bucket websiteConfig deleted");

//******************************* For complete usage, see the following functions  ****************************************************

putBucketWebsite($obsClient, $bucket);
getBucketWebsite($obsClient, $bucket);
deleteBucketWebsite($obsClient, $bucket);
getBucketWebsite($obsClient, $bucket);

/**
 * Sets bucket static website configuration
 *
 * @param $obsClient ObsClient
 * @param  $bucket string bucket name
 * @return null
 */
function putBucketWebsite($obsClient, $bucket)
{
    $websiteConfig = new WebsiteConfig("index.html", "error.html");
    try {
        $obsClient->putBucketWebsite($bucket, $websiteConfig);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket static website configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketWebsite($obsClient, $bucket)
{
    $websiteConfig = null;
    try {
        $websiteConfig = $obsClient->getBucketWebsite($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($websiteConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket static website configuration
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketWebsite($obsClient, $bucket)
{
    try {
        $obsClient->deleteBucketWebsite($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
