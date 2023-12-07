<?php

require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Core\OssUtil;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
//******************************* Simple usage ***************************************************************

// Enable crc64
$options = array(
    $ossClient::OSS_CHECK_CRC64 => true,
);
// Upload the in-memory string (hi, oss) to an OSS file
$object = "b.file";
$content = file_get_contents($object);
$ossClient->putObject($bucket, $object, $content,$options);

// Download an oss object as an in-memory variable
$ossClient->getObject($bucket, $object,$options);

// Append Object
$object = "a.txt";
$filePath = "D:\\localpath\\b.txt";
$filePath1 = "D:\\localpath\\c.txt";
$content = file_get_contents($filePath);
$result = $ossClient->appendObject($bucket, $object,$content,0,$options);
$content1 = file_get_contents($filePath1);
$options[OssClient::OSS_INIT_CRC64] = $result->getCrc();
$result1 = $ossClient->appendObject($bucket, $object, $content1,$result->getPosition(),$options);
$localCrc64 = OssUtil::crc64($content.$content1);
print("append object: OK" . "\n");


// Append File
$object = "a.txt";
$filePath = "D:\\localpath\\b.txt";
$filePath1 = "D:\\localpath\\c.txt";
$result = $ossClient->appendFile($bucket, $filePath,$content,0,$options);
$options[OssClient::OSS_INIT_CRC64] = $result->getCrc();
$result1 = $ossClient->appendObject($bucket, $object, $filePath1,$result->getPosition(),$options);
$localCrc64 = OssUtil::crc64($content.$content1);
print("append file: OK" . "\n");


// Upload Object By multipart upload
$uploadFile = "test/multipart-test.txt";
$object = "oss_test/object.txt";
    /**
 *  step 1. Initialize a block upload event, that is, a multipart upload process to get an upload id
 */
$uploadId = $ossClient->initiateMultipartUpload($bucket, $object);
/*
 * step 2. Upload parts
 */
$partSize = 10 * 1024 * 1024;
$uploadFileSize = sprintf('%u',filesize($uploadFile));
$pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
$responseUploadPart = array();
$uploadPosition = 0;
$checkCrc64 = true;
foreach ($pieces as $i => $piece) {
    $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
    $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
    $upOptions = array(
        $ossClient::OSS_FILE_UPLOAD => $uploadFile,
        $ossClient::OSS_PART_NUM => ($i + 1),
        $ossClient::OSS_SEEK_TO => $fromPos,
        $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
        $ossClient::OSS_CHECK_CRC64 => $checkCrc64,
    );
    //2. Upload each part to OSS
    $resultPart = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
}
$uploadParts = array();
foreach ($responseUploadPart as $i => $eTag) {
    $uploadParts[] = array(
        'PartNumber' => ($i + 1),
        'ETag' => $eTag,
    );
}
/**
 * step 3. Complete the upload
 */
$cmpOptions = null;
$result = $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts,$cmpOptions);



//******************************* For complete usage, see the following functions ****************************************************
putObject($ossClient, $bucket);
getObject($ossClient, $bucket);
appendObject($ossClient,$bucket);
appendFile($ossClient,$bucket);
multipartUpload($ossClient,$bucket);

/**
 * Upload in-memory data to oss
 *
 * Simple upload---upload specified in-memory data to an OSS object
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putObject($ossClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $content = file_get_contents($object);
    $options = array(
        $ossClient::OSS_CHECK_CRC64 => true,
    );
    try {
        $ossClient->putObject($bucket, $object, $content,$options);
        printf("put object success!" . "\n");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get the content of an object.
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getObject($ossClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $options = array(
        OssClient::OSS_CHECK_CRC64 => true
    );
    try {
        $ossClient->getObject($bucket, $object,$options);
        printf("get object success!" . "\n");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Append Object
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 */
function appendObject($ossClient,$bucket){
    $object = "a.txt";
    $filePath = "D:\\localpath\\b.txt";
    $filePath1 = "D:\\localpath\\c.txt";
    $options = array(
        $ossClient::OSS_CHECK_CRC64 => true,
    );
    try{
        $content = file_get_contents($filePath);
        $result = $ossClient->appendObject($bucket, $object,$content,0,$options);
        $options[OssClient::OSS_INIT_CRC64] = $result->getCrc();
        $content1 = file_get_contents($filePath1);
        $ossClient->appendObject($bucket, $object, $content1,$result->getPosition(),$options);
        print("append Object: OK" . "\n");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
}

/**
 * Append File
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 */
function appendFile($ossClient,$bucket){
    $object = "a.txt";
    $filePath = "D:\\localpath\\b.txt";
    $filePath1 = "D:\\localpath\\c.txt";
    $options = array(
        $ossClient::OSS_CHECK_CRC64 => true,
    );
    try{
        $result = $ossClient->appendFile($bucket, $object,$filePath,0,$options);
        $options[OssClient::OSS_INIT_CRC64] = $result->getCrc();
        $ossClient->appendObject($bucket, $object, $filePath1,$result->getPosition(),$options);
        print("append Object: OK" . "\n");
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Use basic multipart upload for file upload.
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @throws OssException
 */
function multipartUpload($ossClient, $bucket)
{
    $uploadFile = $object = "test/multipart-test.txt";
    /**
     *  step 1. Initialize a block upload event, that is, a multipart upload process to get an upload id
     */
    try {
        $uploadId = $ossClient->initiateMultipartUpload($bucket, $object);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": initiateMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
    /*
     * step 2. Upload parts
     */
    $partSize = 10 * 1024 * 1024;
    $uploadFileSize = sprintf('%u',filesize($uploadFile));
    $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
    $responseUploadPart = array();
    $uploadPosition = 0;
    $checkCrc64 = true;
    foreach ($pieces as $i => $piece) {
        $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
        $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
        $upOptions = array(
            $ossClient::OSS_FILE_UPLOAD => $uploadFile,
            $ossClient::OSS_PART_NUM => ($i + 1),
            $ossClient::OSS_SEEK_TO => $fromPos,
            $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
            $ossClient::OSS_CHECK_CRC64 => $checkCrc64,
        );
        //2. Upload each part to OSS
        try {
            $responseUploadPart[] = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
            printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
        } catch (OssException $e) {
            printf($e->getMessage() . "\n");
            return;
        }
    }
    $uploadParts = array();
    foreach ($responseUploadPart as $i => $eTag) {
        $uploadParts[] = array(
            'PartNumber' => ($i + 1),
            'ETag' => $eTag,
        );
    }
    /**
     * step 3. Complete the upload
     */
    try {
        $comOptions = null;
        $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts,$comOptions);
        printf(__FUNCTION__. ": completeMultipartUpload OK\n");
    } catch (OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }

    print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
}
