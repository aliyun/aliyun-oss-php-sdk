<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssUtil;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple usage ***************************************************************

// Upload a file using resumable upload, which determines to use simple upload or multipart upload based on the file size.
$ossClient->resumableUpload($bucket, "a.file", __FILE__, array());
Common::println("local file " . __FILE__ . " is uploaded to the bucket $bucket, a.file");


// Dwonload file useing resumable dwonload
$ossClient->resumableDownload($bucket, "b.file", $options = array(
    OssClient::OSS_FILE_DOWNLOAD => "./c.file",
));
Common::println("b.file is fetched to the local file: c.file");


//******************************* For complete usage, see the following functions ****************************************************

resumableUpload($ossClient,$bucket);
resumableDownload($ossClient,$bucket);

/**
 * Upload files using resumable upload
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function resumableUpload($ossClient, $bucket)
{
    $object = "test/multipart-test.txt";
    $file = __FILE__;
    $options = array();

    try {
        $ossClient->resumableUpload($bucket, $object, $file, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}


/**
 * Download files using resumable download
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function resumableDownload($ossClient, $bucket)
{
    $object = "test/multipart-test.txt";
    $options = array(
        OssClient::OSS_FILE_DOWNLOAD => "./c.file",
    );

    try {
        $ossClient->resumableDownload($bucket, $object, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}