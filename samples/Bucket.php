<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;


$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);
$bucket = Common::getBucketName();

// Get the bucket list
$bucketListInfo = $obsClient->listBuckets();

//$res = $obsClient->createBucket($bucket, ObsClient::OBS_ACL_TYPE_PUBLIC_READ_WRITE);


//$res = $obsClient->createBucket($bucket, ObsClient::OBS_ACL_TYPE_PUBLIC_READ_WRITE);

echo '<pre/>';
print_r($bucketListInfo);
//print_r($res);
exit;
//******************************* Simple Usage****************************************************************

// Create a bucket
$obsClient->createBucket($bucket, ObsClient::OBS_ACL_TYPE_PUBLIC_READ_WRITE);
Common::println("bucket $bucket created");

// Check whether a bucket exists
$doesExist = $obsClient->doesBucketExist($bucket);
Common::println("bucket $bucket exist? " . ($doesExist ? "yes" : "no"));

// Get the bucket list
$bucketListInfo = $obsClient->listBuckets();

// Set bucket ACL
$obsClient->putBucketAcl($bucket, ObsClient::OBS_ACL_TYPE_PUBLIC_READ_WRITE);
Common::println("bucket $bucket acl put");
// Get bucket ACL
$acl = $obsClient->getBucketAcl($bucket);
Common::println("bucket $bucket acl get: " . $acl);


//******************************* For complete usage, see the following functions ****************************************************

createBucket($obsClient, $bucket);
doesBucketExist($obsClient, $bucket);
deleteBucket($obsClient, $bucket);
putBucketAcl($obsClient, $bucket);
getBucketAcl($obsClient, $bucket);
listBuckets($obsClient);

/**
 * Create a new bucket
 * acl indicates the access permission of a bucket, including: private, public-read-only/private-read-write, and public read-write.
 * Private indicates that only the bucket owner or authorized users can access the data..
 * The three permissions are separately defined by (ObsClient::OBS_ACL_TYPE_PRIVATE,ObsClient::OBS_ACL_TYPE_PUBLIC_READ, ObsClient::OBS_ACL_TYPE_PUBLIC_READ_WRITE)
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 */
function createBucket($obsClient, $bucket)
{
    try {
        $obsClient->createBucket($bucket, ObsClient::OBS_ACL_TYPE_PUBLIC_READ_WRITE);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Check whether a bucket exists.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 */
function doesBucketExist($obsClient, $bucket)
{
    try {
        $res = $obsClient->doesBucketExist($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    if ($res === true) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    }
}

/**
 * Delete a bucket. If the bucket is not empty, the deletion fails.
 * A bucket which is not empty indicates that it does not contain any objects or parts that are not completely uploaded during multipart upload
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket Name of the bucket to delete
 * @return null
 */
function deleteBucket($obsClient, $bucket)
{
    try {
        $obsClient->deleteBucket($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Set bucket ACL
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketAcl($obsClient, $bucket)
{
    $acl = ObsClient::OBS_ACL_TYPE_PRIVATE;
    try {
        $obsClient->putBucketAcl($bucket, $acl);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get bucket ACL
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketAcl($obsClient, $bucket)
{
    try {
        $res = $obsClient->getBucketAcl($bucket);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print('acl: ' . $res);
}


/**
 * List all buckets
 *
 * @param ObsClient $obsClient ObsClient instance
 * @return null
 */
function listBuckets($obsClient)
{
    $bucketList = null;
    try {
        $bucketListInfo = $obsClient->listBuckets();
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $bucketList = $bucketListInfo->getBucketList();
    foreach ($bucketList as $bucket) {
        print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
    }
}
