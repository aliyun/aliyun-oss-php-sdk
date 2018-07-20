<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsUtil;
use OBS\Core\ObsException;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//******************************* Simple usage ***************************************************************

/**
 * See the putObjectByRawAPis usage in complete example to check out basic multipart upload APIs which can be used as resumable upload.
 */

// Upload a file using the multipart upload interface, which determines to use simple upload or multipart upload based on the file size.
$obsClient->multiuploadFile($bucket, "file.php", __FILE__, array());
Common::println("local file " . __FILE__ . " is uploaded to the bucket $bucket, file.php");


// Upload local directory's data into target dir
$obsClient->uploadDir($bucket, "targetdir", __DIR__);
Common::println("local dir " . __DIR__ . " is uploaded to the bucket $bucket, targetdir/");


// List the incomplete multipart uploads
$listMultipartUploadInfo = $obsClient->listMultipartUploads($bucket, array());


//******************************* For complete usage, see the following functions ****************************************************

multiuploadFile($obsClient, $bucket);
putObjectByRawApis($obsClient, $bucket);
uploadDir($obsClient, $bucket);
listMultipartUploads($obsClient, $bucket);

/**
 * Upload files using multipart upload
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function multiuploadFile($obsClient, $bucket)
{
    $object = "test/multipart-test.txt";
    $file = __FILE__;
    $options = array();

    try {
        $obsClient->multiuploadFile($bucket, $object, $file, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}

/**
 * Use basic multipart upload for file upload.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @throws ObsException
 */
function putObjectByRawApis($obsClient, $bucket)
{
    $object = "test/multipart-test.txt";
    /**
     *  step 1. Initialize a block upload event, that is, a multipart upload process to get an upload id
     */
    try {
        $uploadId = $obsClient->initiateMultipartUpload($bucket, $object);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": initiateMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
    /*
     * step 2. Upload parts
     */
    $partSize = 10 * 1024 * 1024;
    $uploadFile = __FILE__;
    $uploadFileSize = filesize($uploadFile);
    $pieces = $obsClient->generateMultiuploadParts($uploadFileSize, $partSize);
    $responseUploadPart = array();
    $uploadPosition = 0;
    $isCheckMd5 = true;
    foreach ($pieces as $i => $piece) {
        $fromPos = $uploadPosition + (integer)$piece[$obsClient::OBS_SEEK_TO];
        $toPos = (integer)$piece[$obsClient::OBS_LENGTH] + $fromPos - 1;
        $upOptions = array(
            $obsClient::OBS_FILE_UPLOAD => $uploadFile,
            $obsClient::OBS_PART_NUM => ($i + 1),
            $obsClient::OBS_SEEK_TO => $fromPos,
            $obsClient::OBS_LENGTH => $toPos - $fromPos + 1,
            $obsClient::OBS_CHECK_MD5 => $isCheckMd5,
        );
        if ($isCheckMd5) {
            $contentMd5 = ObsUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
            $upOptions[$obsClient::OBS_CONTENT_MD5] = $contentMd5;
        }
        //2. Upload each part to OBS
        try {
            $responseUploadPart[] = $obsClient->uploadPart($bucket, $object, $uploadId, $upOptions);
        } catch (ObsException $e) {
            printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
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
        $obsClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * Upload by directories
 *
 * @param ObsClient $obsClient ObsClient
 * @param string $bucket bucket name
 *
 */
function uploadDir($obsClient, $bucket)
{
    $localDirectory = ".";
    $prefix = "samples/codes";
    try {
        $obsClient->uploadDir($bucket, $prefix, $localDirectory);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * Get ongoing multipart uploads
 *
 * @param $obsClient ObsClient
 * @param $bucket   string
 */
function listMultipartUploads($obsClient, $bucket)
{
    $options = array(
        'max-uploads' => 100,
        'key-marker' => '',
        'prefix' => '',
        'upload-id-marker' => ''
    );
    try {
        $listMultipartUploadInfo = $obsClient->listMultipartUploads($bucket, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": listMultipartUploads FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": listMultipartUploads OK\n");
    $listUploadInfo = $listMultipartUploadInfo->getUploads();
    var_dump($listUploadInfo);
}
