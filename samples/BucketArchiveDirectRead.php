<?php

require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// Set bucket's archive direct read
$enabled = true;
$ossClient->putBucketArchiveDirectRead($bucket, $enabled);
printf('Set Bucket Archive Direct Read Success' . "\n");


// get bucket's archive direct read
$result  = $ossClient->getBucketArchiveDirectRead($bucket);
printf('Bucket Archive Direct Read Enabled:%s'."\n",var_export($result,true));



//******************************* For complete usage, see the following functions ****************************************************
putBucketArchiveDirectRead($ossClient,$bucket);
getBucketArchiveDirectRead($bucket);

/**
 * Put Bucket Archive Direct Read
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws \OSS\Http\RequestCore_Exception
 */
function putBucketArchiveDirectRead($ossClient, $bucket)
{
	try{
        $enabled = true;
        $ossClient->putBucketArchiveDirectRead($bucket, $enabled);
        printf('Set Bucket Archive Direct Read Success' . "\n");
	} catch(OssException $e) {
		printf($e->getMessage() . "\n");
		return;
	}
	print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Bucket Archive Direct Read
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws \OSS\Http\RequestCore_Exception
 */
function getBucketArchiveDirectRead($ossClient, $bucket)
{
	try{
        $result  = $ossClient->getBucketArchiveDirectRead($bucket);
        printf('Bucket Archive Direct Read Enabled:%s', var_export($result,true));
	} catch(OssException $e) {
		printf($e->getMessage() . "\n");
		return;
	}
	print(__FUNCTION__ . ": OK" . "\n");
}
