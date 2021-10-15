<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssUtil;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple usage ***************************************************************

/**
 * See the putObjectByRawAPis usage in complete example to check out basic multipart upload APIs which can be used as resumable upload.
 */

// Upload a file using the multipart upload interface, which determines to use simple upload or multipart upload based on the file size.
$ossClient->multiuploadFile($bucket, "file.php", __FILE__, array());
Common::println("local file " . __FILE__ . " is uploaded to the bucket $bucket, file.php");


// Upload local directory's data into target dir
$ossClient->uploadDir($bucket, "targetdir", __DIR__);
Common::println("local dir " . __DIR__ . " is uploaded to the bucket $bucket, targetdir/");


// List the incomplete multipart uploads
$listMultipartUploadInfo = $ossClient->listMultipartUploads($bucket, array());


//******************************* For complete usage, see the following functions ****************************************************

multiuploadFile($ossClient, $bucket);
putObjectByRawApis($ossClient, $bucket);
uploadDir($ossClient, $bucket);
listMultipartUploads($ossClient, $bucket);
$checkpointFile = "<uploadFile>.ucp";
/**
 *  the content of checkpointFile
* {
	"uploadId":"F1B0FF9986B6487884906EEF89BC78AF",
    "object":"<objectName>",
    "uploadFile":"\/mnt\/d\/www\/nextcloud-22.1.1.zip",
    "partSize":31457280,
    "parts":[
		"\"EFDB0F4AB77D19CEAC64F6A25009B965\"",
		"\"969AAFA5AC9CE01F55B68D5AF51319E6\"",
		"\"172E8F884C09FE71DE518E872F9C680E\""
	]
}
*/
resumeUpload($ossClient,$bucket,$checkpointFile);

/**
 * Upload files using multipart upload
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function multiuploadFile($ossClient, $bucket)
{
    $object = "test/multipart-test.txt";
    $file = __FILE__;
    $options = array();

    try {
        $ossClient->multiuploadFile($bucket, $object, $file, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}

/**
 * Use basic multipart upload for file upload.
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @throws OssException
 */
function putObjectByRawApis($ossClient, $bucket)
{
    $object = "test/multipart-test.txt";
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
    $uploadFile = __FILE__;
    $uploadFileSize = sprintf('%u',filesize($uploadFile));
    $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
    $responseUploadPart = array();
    $uploadPosition = 0;
    $isCheckMd5 = true;
    foreach ($pieces as $i => $piece) {
        $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
        $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
        $upOptions = array(
            $ossClient::OSS_FILE_UPLOAD => $uploadFile,
            $ossClient::OSS_PART_NUM => ($i + 1),
            $ossClient::OSS_SEEK_TO => $fromPos,
            $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
            $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
        );
        if ($isCheckMd5) {
            $contentMd5 = OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
            $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
        }
        //2. Upload each part to OSS
        try {
            $responseUploadPart[] = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
        } catch (OssException $e) {
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
        $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * Upload by directories
 *
 * @param OssClient $ossClient OssClient
 * @param string $bucket bucket name
 *
 */
function uploadDir($ossClient, $bucket)
{
    $localDirectory = ".";
    $prefix = "samples/codes";
    try {
        $ossClient->uploadDir($bucket, $prefix, $localDirectory);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * Get ongoing multipart uploads
 *
 * @param $ossClient OssClient
 * @param $bucket   string
 */
function listMultipartUploads($ossClient, $bucket)
{
    $options = array(
        'max-uploads' => 100,
        'key-marker' => '',
        'prefix' => '',
        'upload-id-marker' => ''
    );
    try {
        $listMultipartUploadInfo = $ossClient->listMultipartUploads($bucket, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": listMultipartUploads FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": listMultipartUploads OK\n");
    $listUploadInfo = $listMultipartUploadInfo->getUploads();
    var_dump($listUploadInfo);
}

/**
 * resume file
 * @param $ossClient OSS\OssClient
 * @param $bucket string
 */
function resumeUpload($ossClient, $bucket,$file)
{
	/**
	 * step 1. read parts info
	 */
	$str = file_get_contents($file);
	$uploadInfo = json_decode($str,true);
	$uploadId = $uploadInfo['uploadId'];
	$parts = $uploadInfo['parts'];
	$object = $uploadInfo['object'];
	/**
	 * step 2. Upload parts
	 */
	$partSize = $uploadInfo['partSize'];
	$uploadFile = $uploadInfo['uploadFile'];
	$uploadFileSize = filesize($uploadFile);
	$pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
	$responseUploadPart = array();
	$uploadPosition = 0;
	$isCheckMd5 = true;
	$num = count($parts);
	foreach ($pieces as $i => $piece) {
		if($i < $num){
			continue;
		}
		$fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
		$toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
		$upOptions = array(
			$ossClient::OSS_FILE_UPLOAD => $uploadFile,
			$ossClient::OSS_PART_NUM => ($i + 1),
			$ossClient::OSS_SEEK_TO => $fromPos,
			$ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
			$ossClient::OSS_CHECK_MD5 => $isCheckMd5,
		);
		if ($isCheckMd5) {
			$contentMd5 = OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
			$upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
		}
		//2. Upload each part to OSS
		try {
			$responseUploadPart[] = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
		} catch (OssException $e) {
			printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
			printf($e->getMessage() . "\n");
			return;
		}
		printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
	}
	$responseUploadPart = array_merge($parts,$responseUploadPart);
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
		$ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}
