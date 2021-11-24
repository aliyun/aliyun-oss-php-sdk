<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssUtil;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple usage ***************************************************************

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
}*/
putObjectByRawApis($ossClient,$bucket);
resumeUpload($ossClient,$bucket,$checkpointFile);
// pieces download
$downloadUcp = "<downloadFile>.ucp";
/**
 * *{
"object":"nextcloud-22.zip",
"partSize":20971520,
"pieces":[
{
"seekTo":0,
"length":20971520
},
{
"seekTo":20971520,
"length":20971520
},
],
"parts":2
}
 */
resumeDownloadByRawApi($ossClient,$bucket);
resumeDownload($ossClient,$bucket,$downloadUcp);
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
	$uploadInfo = array(
		'uploadId' =>$uploadId,
		'object'=>$object,
		'uploadFile'=>$uploadFile,
		'partSize'=>$partSize,
	);
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
			$uploadInfo['parts'] = $responseUploadPart;
			// 上传分片信息存储到文件中
			file_put_contents('upload.ucp',json_encode($uploadInfo));
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



/**
 * @param $ossClient OSS\OssClient
 * @param $bucket string
 */
function resumeDownloadByRawApi($ossClient, $bucket)
{
	// Get information about object
	$object = 'test-object-name';
	$objectMeta = $ossClient->getObjectMeta($bucket, $object);
	$size = $objectMeta['content-length'];
	$partSize =1024*1024*20;
	// Initialize fragment information
	$pieces = $ossClient->generateMultiuploadParts($size, $partSize);
	$parts = array();
	$downloadPosition = 0;
	$num = count($parts);
	// File information and slices are stored in the array
	$downloadArray = array(
		"object" => $object,
		"partSize" => 1024*1024*20,
		'fileSize' => $size,
		"pieces" => $pieces,
	);
	
	foreach ($pieces as $i => $piece) {
		if($i < $num){
			continue;
		}
		$fromPos = $downloadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
		$toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
		$downOptions = array(
			OssClient::OSS_RANGE => $fromPos.'-'.$toPos
		);
		
		$content = $ossClient->getObject($bucket,$object,$downOptions);
		// Store downloaded shards
		file_put_contents($object, $content,FILE_APPEND );
		
		$downloadArray['parts'] = $i+1;
		// Store fragment information (downloaded fragments, object name, fragment size, number of fragments, etc.)
		file_put_contents('download.ucp', json_encode($downloadArray) );
		
		printf(__FUNCTION__.":resume download, part - part#{$i} OK\n");
	}
	
	printf( __FUNCTION__.":Object ".$object.'download complete');
}


/**
 * @param $ossClient OssClient
 * @param string $bucket BucketName
 * @param string $file Localpath
 */
function resumeDownload($ossClient, $bucket,$file)
{
	$str = file_get_contents('download.ucp');
	$downloadInfo = json_decode($str,true);
	$num = $downloadInfo['parts'];
	$pieces = $downloadInfo['pieces'];
	$object = $downloadInfo['object'];
	$downloadPosition = 0;
	
	foreach ($pieces as $i => $piece) {
		if($i < $num){
			continue;
		}
		$fromPos = $downloadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
		$toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
		$downOptions = array(
			OssClient::OSS_RANGE => $fromPos.'-'.$toPos
		);
		
		$content = $ossClient->getObject($bucket,$object,$downOptions);
		// Store downloaded shards
		file_put_contents($object, $content,FILE_APPEND );
		
		$downloadArray['parts'] = $i+1;
		// Store fragment information (downloaded fragments, object name, fragment size, number of fragments, etc.)
		file_put_contents('download.ucp', json_encode($downloadArray) );
		
		printf(__FUNCTION__.":resume download, part - part#{$i} OK\n");
	}
	
	printf( __FUNCTION__.":Object ".$object.'download complete');
}



