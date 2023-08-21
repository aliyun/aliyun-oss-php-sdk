<?php

require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// put access monitor
$status = 'Enabled';
$ossClient->putBucketAccessMonitor($bucket, $status);
printf('Put Bucket Access Monitor Success' . "\n");


// get access monitor
$status  = $ossClient->getBucketAccessMonitor($bucket);
printf('Get Bucket Access Monitor Status:%s'."\n",$status);


//******************************* For complete usage, see the following functions ****************************************************
putBucketAccessMonitor($ossClient,$bucket);
getBucketAccessMonitor($ossClient,$bucket);

/**
 * putBucketAccessMonitor
 * @param $ossClient OssClient
 * @param string $bucket bucket_name string
 */
function putBucketAccessMonitor($ossClient, $bucket)
{
    try{
        $status = 'Enabled'; // set Enabled to enable access monitor; set Disabled to disable access monitor
        $ossClient->putBucketTransferAcceleration($bucket,$status);
        printf('Put Bucket Access Monitor Success' . "\n");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * @param $ossClient OssClient
 * @param string $bucket bucket_name
 */
function getBucketAccessMonitor($ossClient, $bucket)
{
    try{
        $status = $ossClient->getBucketAccessMonitor($bucket);
        printf('Get Bucket Access Monitor Status:%s'."\n",$status);
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
