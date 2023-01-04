<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\StyleConfig;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// put style
$config = new StyleConfig();
$config->setName("image-style");
$config->setContent("image/resize,p_50");
$ossClient->putBucketStyle($bucket, $config);
printf('Put Bucket Style Success' . "\n");

// get style
$result = $ossClient->getBucketStyle($bucket,"image-style");
printf('Bucket Style Name:%s' . "\n",$result->getName());
printf('Bucket Style Content:%s' . "\n",$result->getContent());
printf('Bucket Style Create Time:%s' . "\n",$result->getCreateTime());
printf('Bucket Style Last Modify Time:%s' . "\n",$result->getLastModifyTime());

// list style
$result = $ossClient->listBucketStyle($bucket);
foreach ($result->getStyleList() as $style){
    printf('======================= Bucket Style Config ===================' . "\n");
    printf('Bucket Style Name:%s' . "\n",$style->getName());
    printf('Bucket Style Content:%s' . "\n",$style->getContent());
    printf('Bucket Style Create Time:%s' . "\n",$style->getCreateTime());
    printf('Bucket Style Last Modify Time:%s' . "\n",$style->getLastModifyTime());
}

// delete style
$ossClient->deleteBucketStyle($bucket,'image-style');
printf('Delete Bucket Style Success' . "\n");


//******************************* For complete usage, see the following functions ****************************************************

putBucketStyle($ossClient, $bucket);
getBucketStyle($ossClient, $bucket);
listBucketStyle($ossClient, $bucket);
deleteBucketStyle($ossClient, $bucket);

/**
 * Put Bucket Style
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 */
function putBucketStyle($ossClient, $bucket)
{
	try {
        $config = new StyleConfig();
        $config->setName("image-style");
        $config->setContent("image/resize,p_50");
        $ossClient->putBucketStyle($bucket, $config);
        printf('Put Bucket Style Success' . "\n");
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	
	print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get Bucket Style
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 */
function getBucketStyle($ossClient, $bucket)
{
	try {
        $result = $ossClient->getBucketStyle($bucket,"image-style");
        printf('Bucket Style Name:%s' . "\n",$result->getName());
        printf('Bucket Style Content:%s' . "\n",$result->getContent());
        printf('Bucket Style Create Time:%s' . "\n",$result->getCreateTime());
        printf('Bucket Style Last Modify Time:%s' . "\n",$result->getLastModifyTime());
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	
	print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * List Bucket Style
 * @param $ossClient $ossClient OssClient instance
 * @param $bucket $bucket Name of the bucket to create
 */
function listBucketStyle($ossClient, $bucket)
{
	try {
        $result = $ossClient->listBucketStyle($bucket);
        foreach ($result->getStyleList() as $style){
            printf('======================= Bucket Style Config ===================' . "\n");
            printf('Bucket Style Name:%s' . "\n",$style->getName());
            printf('Bucket Style Content:%s' . "\n",$style->getContent());
            printf('Bucket Style Create Time:%s' . "\n",$style->getCreateTime());
            printf('Bucket Style Last Modify Time:%s' . "\n",$style->getLastModifyTime());
        }
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	
	print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Delete Bucket Style
 * @param $ossClient $ossClient OssClient instance
 * @param $bucket $bucket Name of the bucket to create
 */
function deleteBucketStyle($ossClient, $bucket)
{
	try {
		$ossClient->deleteBucketStyle($bucket,'image-style');
        printf('Delete Bucket Style Success' . "\n");
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	
	print(__FUNCTION__ . ": OK" . "\n");
}