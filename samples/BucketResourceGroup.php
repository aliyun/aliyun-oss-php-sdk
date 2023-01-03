<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// put resource group
$resourceGroupId = 'rg-xxxxxx';
$ossClient->putBucketResourceGroup($bucket, $resourceGroupId);
printf('Put Bucket Resource Group Success' . "\n");


// get resource group
$resourceGroupId = $ossClient->getBucketResourceGroup($bucket);
printf('Get Bucket Resource Group Id:%s'."\n",$resourceGroupId);


//******************************* For complete usage, see the following functions ****************************************************
putBucketResourceGroup($ossClient,$bucket);
getBucketResourceGroup($ossClient,$bucket);

/**
 * Put Bucket Resource Group
 * @param $ossClient OssClient
 * @param string $bucket bucket_name string
 */
function putBucketResourceGroup($ossClient, $bucket)
{
    try{
        $resourceGroupId = 'rg-xxxxxx';
        $ossClient->putBucketResourceGroup($bucket,$resourceGroupId);
        printf('Put Bucket Resource Group Success' . "\n");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Bucket Resource Group Id
 * @param $ossClient OssClient
 * @param string $bucket bucket_name
 */
function getBucketResourceGroup($ossClient, $bucket)
{
    try{
        $resourceGroupId = $ossClient->getBucketResourceGroup($bucket);
        printf('Get Bucket Resource Group Id:%s'."\n",$resourceGroupId);
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}