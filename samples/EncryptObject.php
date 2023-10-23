<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use Oss\OssEncryptionClient;
use OSS\Crypto\RsaProvider;
use OSS\Core\OssUtil;
use OSS\Crypto\RsaEncryptionMaterials;
use OSS\Crypto\KmsEncryptionMaterials;
use Oss\Crypto\KmsProvider;
use OSS\Core\OssException;
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
//---------------------------------------------------------------encrypted sample ----------------------------------------------------

$publicKey = <<<BBB
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3D*****
-----END PUBLIC KEY-----
BBB;

$privateKey = <<<BBB
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDM7VUsLBFNFD2G8mOpRhmiA9x4BKqhW21P2h776wwqT2/OJqGc
*****
-----END RSA PRIVATE KEY-----
BBB;
// Upload the in-memory string (hi, oss) to an OSS file by Rsa
$content = 'hello php ,I am a phper';
$keys = array(
    'public_key' => $publicKey,
    'private_key' => $privateKey
);
$matDesc= array(
    'key1'=>'test-one'
);
$provider= new RsaProvider($keys,$matDesc);
// Use and manage multiple keys
/*
 *
$publicKey2 = <<<BBB
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3D*****
-----END PUBLIC KEY-----
BBB;

$privateKey2 = <<<BBB
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDM7VUsLBFNFD2G8mOpRhmiA9x4BKqhW21P2h776wwqT2/OJqGc
*****
-----END RSA PRIVATE KEY-----
BBB;
$keys2 = array(
	'public_key' => $publicKey2,
	'private_key' => $privateKey2
);
$cipher = new AesCtrCipher();
$matDesc2= array(
	'key2'=>'test-one'
);
$encryptionMaterials = new RsaEncryptionMaterials($matDesc2,$keys2);
$provider->addEncryptionMaterials($encryptionMaterials);
*/

$ossEncryptionClient = Common::getOssEncryptionClient($provider);
$result = $ossEncryptionClient->putObject($bucket,'encrypt.txt',$content);
Common::println("encrypt.txt is created");


// download the object by rsa encrypt
$content = $ossEncryptionClient->getObject($bucket,'encrypt.txt');
Common::println("encrypt.txt is fetched, the content is: " . $content);


// Upload and download by kms
$matDesc= array(
    'key2'=>'test-kms'
);

$cmkId= '*****-ccff-4a4f-8fb3-cdc7695fa67c';
$provider= new KmsProvider(Config::OSS_ACCESS_ID,Config::OSS_ACCESS_KEY,Config::KMS_ENDPOINT,$cmkId,$matDesc);

// Kms Use and manage multiple keys
$matDesc2 = array(
    'test-kms-one'=>'test-kms-one'
);
$otherKmsRegion = Config::OSS_ENDPOINT;
$cmkId2= '*****-ccff-4a4f-8fb3-cdc76957bcd';
$encryptionMaterials = new KmsEncryptionMaterials($matDesc2,$otherKmsRegion,$cmkId2);
$provider->addEncryptionMaterials($encryptionMaterials);
$ossEncryptionClient = Common::getOssEncryptionClient($provider);

// kms upload
$result = $ossEncryptionClient->putObject($bucket,'kms.php',file_get_contents(__FILE__));
// kms download
$result = $ossEncryptionClient->getObject($bucket,'kms.php');

// =========================================Let's take the rsa encryption client as an example=====================================================================
// Multi Part Upload
$ossEncryptionClient = Common::getOssEncryptionClient($provider);
$partSize = 5 * 1024 * 1024;
$uploadFile = 'your-local-file';
$uploadFileSize = sprintf('%u',filesize($uploadFile));

$object = "object-name";
$options['headers'] = array(
    OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_DATA_SIZE => $uploadFileSize,
    OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_PART_SIZE=>$partSize
);
try {
    /**
     *  step 1. Initialize a block upload event, that is, a multipart upload process to get an upload id
     */
    $uploadId = $ossEncryptionClient->initiateMultipartUpload($bucket, $object,$options);
    $pieces = $ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
    /**
     * step 2. Upload parts
     */
    $responseUploadPart = array();
    $uploadPosition = 0;
    foreach ($pieces as $i => $piece) {
        $fromPos = $uploadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
        $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
        $content = OssUtil::getDataFromFile($uploadFile,$fromPos,$toPos);
        $upOptions = array(
            OssClient::OSS_PART_NUM => ($i + 1),
            OssClient::OSS_CONTENT => $content,
        );
        $responseUploadPart[] = $ossEncryptionClient->uploadPart($bucket, $object, $uploadId, $upOptions);
        printf( "initiateMultipartUpload, uploadPart - part#{$i} OK\n");
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
    $ossEncryptionClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
}catch (OssException $e) {
    printf($e->getMessage() . "\n");
    return;
}
printf("completeMultipartUpload OK\n");


// Multi Part Download
$object = "object-name";
$download = 'local-file-name';
$ossEncryptionClient = Common::getOssEncryptionClient($provider);
$objectMeta = $ossEncryptionClient->getObjectMeta($bucket, $object);
$size = $objectMeta['content-length'];
$partSize =1024*1024*5;

$pieces = $ossEncryptionClient->generateMultiuploadParts($size, $partSize);
$downloadPosition = 0;

foreach ($pieces as $i => $piece) {
    $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
    $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
    $downOptions = array(
        OssClient::OSS_RANGE => $fromPos.'-'.$toPos
    );
    $content = $ossEncryptionClient->getObject($bucket,$object,$downOptions);
    file_put_contents($download, $content,FILE_APPEND );
    printf("Multi download, part - part#{$i} OK\n");
}

printf( "Object ".$object.'download complete');

// Range download
$ossEncryptionClient = Common::getOssEncryptionClient($provider);

// The starting position of the file must be an integer multiple of 16
$options = array(
    OssClient::OSS_RANGE => '48-100'
);
$content = $ossEncryptionClient->getObject($bucket, $object,$options);
printf($object . " is fetched, the content is: " . $content);

// Resumable upload ====================

$ossEncryptionClient = Common::getOssEncryptionClient($provider);
//Read the content of the record file, including fragment information, upload file, uploadId and other information
$str = file_get_contents("download.ucp");
$uploadInfo = json_decode($str,true);
$uploadId = $uploadInfo['uploadId'];
$parts = $uploadInfo['parts'];
$object = $uploadInfo['object'];

$partSize = $uploadInfo['partSize'];
$uploadFile = $uploadInfo['uploadFile'];
$uploadFileSize = sprintf('%u',filesize($uploadFile));
/**
 * step 1. Upload parts
 */
$pieces = $ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
$responseUploadPart = array();
$uploadPosition = 0;
$num = count($parts);
foreach ($pieces as $i => $piece) {
    if($i < $num){
        continue;
    }
    $fromPos = $uploadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
    $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
    $content = OssUtil::getDataFromFile($uploadFile,$fromPos,$toPos);
    $upOptions = array(
        OssClient::OSS_PART_NUM => ($i + 1),
        OssClient::OSS_CONTENT => $content,
    );
    $responseUploadPart[] = $ossEncryptionClient->uploadPart($bucket, $object, $uploadId, $upOptions);
    printf("initiateMultipartUpload, uploadPart - part#{$i} OK\n");
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
 * step 2. Complete the upload
 */
$ossEncryptionClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
printf("completeMultipartUpload OK\n");


// Resumable Download ==================================================

$ossEncryptionClient = Common::getOssEncryptionClient($provider);
//Read the content of the record file, including fragment information, upload file, uploadId and other information
$str = file_get_contents('download.ucp');
// Read the related information of the object from the file
/**  The file content format is as follows
 *{
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
$downloadInfo = json_decode($str,true);
$num = $downloadInfo['parts'];
$pieces = $downloadInfo['pieces'];
$object = $downloadInfo['object'];
$downloadPosition = 0;
foreach ($pieces as $i => $piece) {
    if($i < $num){
        continue;
    }
    $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
    $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
    $downOptions = array(
        OssClient::OSS_RANGE => $fromPos.'-'.$toPos
    );
    $content = $ossEncryptionClient->getObject($bucket,$object,$downOptions);
    file_put_contents($download, $content,FILE_APPEND );
    printf("resume download, part - part#{$i} OK\n");
}
printf( "Object ".$object.'download complete'.PHP_EOL);



//******************************* For complete usage, see the following functions ****************************************************

putObject($ossEncryptionClient, $bucket);
getObject($ossEncryptionClient, $bucket);
mutipartUpload($ossEncryptionClient, $bucket);
mutipartDownload($ossEncryptionClient,$bucket);
rangeDownload($ossEncryptionClient,$bucket);
resumeUpload($ossEncryptionClient,$bucket);
resumeDownload($ossEncryptionClient,$bucket);


/**
 * Upload a encrypt data to an OSS object
 * Simple upload
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 */
function putObject($ossEncryptionClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $content = file_get_contents(__FILE__);
    $options = array();
    try {
        $ossEncryptionClient->putObject($bucket, $object, $content, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get the content of an object.
 * Simple download
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getObject($ossEncryptionClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $options = array();
    try {
        $content = $ossEncryptionClient->getObject($bucket, $object, $options);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    if (file_get_contents(__FILE__) === $content) {
        print(__FUNCTION__ . ": FileContent checked OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FileContent checked FAILED" . "\n");
    }
}





/**
 * Use basic multipart for file Upload.
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 * @return null
 */
function mutipartUpload($ossEncryptionClient, $bucket)
{
    try {
        $partSize = 5 * 1024 * 1024;
        $uploadFile = 'your-local-file';
        $uploadFileSize = sprintf('%u',filesize($uploadFile));
        $object = "object-name";
        // Set header metadata
        $options['headers'] = array(
            OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_DATA_SIZE => $uploadFileSize,
            OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_PART_SIZE=>$partSize
        );
        // Initialize segment upload and get a uoloadId
        $uploadId = $ossEncryptionClient->initiateMultipartUpload($bucket, $object,$options);
        $pieces = $ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        // Upload part content
        foreach ($pieces as $i => $piece) {
            $fromPos = $uploadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
            $content = OssUtil::getDataFromFile($uploadFile,$fromPos,$toPos);
            $upOptions = array(
                OssClient::OSS_PART_NUM => ($i + 1),
                OssClient::OSS_CONTENT => $content,
            );
            $responseUploadPart[] = $ossEncryptionClient->uploadPart($bucket, $object, $uploadId, $upOptions);
            printf( __FUNCTION__ ."initiateMultipartUpload, uploadPart - part#{$i} OK\n");
        }
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        // Upload part completed
        $ossEncryptionClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    }catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    printf(__FUNCTION__ .": completeMultipartUpload OK\n");
}

/**
 * Use basic multipart upload for file download.
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 * @return null
 */

function mutipartDownload($ossEncryptionClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $download = 'your-local-file-name';
    try {
        $objectMeta = $ossEncryptionClient->getObjectMeta($bucket, $object);
        $size = $objectMeta['content-length'];
        $partSize = 5 * 1024 * 1024;
        $pieces = $ossEncryptionClient->generateMultiuploadParts($size, $partSize);
        $responseUploadPart = array();
        $downloadPosition = 0;
        // download part content
        foreach ($pieces as $i => $piece) {
            $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
            $downOptions = array(
                OssClient::OSS_RANGE => $fromPos.'-'.$toPos
            );
            $content = $ossEncryptionClient->getObject($bucket, $object,$downOptions);
            file_put_contents($download, $content,FILE_APPEND );
            printf( __FUNCTION__ ."initiateMultipartDownload, downloadPart - part#{$i} OK\n");
        }
    }catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    printf(__FUNCTION__ .": complete Multipart Download OK\n");
}

/**
 * Range file download
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 * @return null
 */
function rangeDownload($ossEncryptionClient,$bucket){
    try {
        $object = "oss-php-sdk-test/upload-test-object-name.txt";
        $options = array(
            OssClient::OSS_RANGE => '48-100'
        );
        $content = $ossEncryptionClient->getObject($bucket, $object,$options);
    }catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ .": Range Download OK\n");
    printf($object . " is fetched, the content is: " . $content);
}

/**
 * Resume Upload for a big file
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 * @return null
 */
function resumeUpload($ossEncryptionClient,$bucket){
    $str = file_get_contents("upload.ucp");
    $uploadInfo = json_decode($str,true);
    $uploadId = $uploadInfo['uploadId'];
    $parts = $uploadInfo['parts'];
    $object = $uploadInfo['object'];
    $partSize = $uploadInfo['partSize'];
    $uploadFile = $uploadInfo['uploadFile'];
    $uploadFileSize = sprintf('%u',filesize($uploadFile));
    try {
        $pieces = $ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        $num = count($parts);
        foreach ($pieces as $i => $piece) {
            if($i < $num){
                continue;
            }
            $fromPos = $uploadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
            $content = OssUtil::getDataFromFile($uploadFile,$fromPos,$toPos);
            $upOptions = array(
                OssClient::OSS_PART_NUM => ($i + 1),
                OssClient::OSS_CONTENT => $content,
            );
            $responseUploadPart[] = $ossEncryptionClient->uploadPart($bucket, $object, $uploadId, $upOptions);
            printf( "resume upload, uploadPart - part#{$i} OK\n");
        }
        $responseUploadPart = array_merge($parts,$responseUploadPart);
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        $ossEncryptionClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    }catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
    }
    printf(__FUNCTION__ . "resume upload OK\n");
}
/**
 * Resume Download for a big file
 * @param OssEncryptionClient $ossEncryptionClient OssEncryptionClient instance
 * @param string $bucket bucket name
 * @return null
 */
function resumeDownload($ossEncryptionClient,$bucket){
    try {
        $str = file_get_contents('download.ucp');
        $downloadInfo = json_decode($str,true);
        $num = $downloadInfo['parts'];
        $pieces = $downloadInfo['pieces'];
        $object = $downloadInfo['object'];
        $download = 'your-local-file';
        $downloadPosition = 0;
        foreach ($pieces as $i => $piece) {
            if($i < $num){
                continue;
            }
            $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
            $downOptions = array(
                OssClient::OSS_RANGE => $fromPos.'-'.$toPos
            );
            $content = $ossEncryptionClient->getObject($bucket,$object,$downOptions);
            file_put_contents($download, $content,FILE_APPEND );
            printf("resume download, part - part#{$i} OK\n");
        }
    }catch (OssException $e){
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf( "Object ".$object.' resume download complete');
}











