<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Core\OssUtil;
use OSS\Crypto\KmsEncryptionMaterials;
use Oss\Crypto\KmsProvider;
use OSS\Crypto\RsaEncryptionMaterials;
use OSS\Crypto\RsaProvider;
use OSS\OssClient;
use Oss\OssEncryptionClient;
require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Common.php';

class OssEncryptionClientObjectTest extends TestOssClientBase
{

    private $ossEncryptionClient;

    private $publicKey = <<<BBB
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDM7VUsLBFNFD2G8mOpRhmiA9x4
BKqhW21P2h776wwqT2/OJqGcBSF/EZJOK/MQqRSOqxNUYdwVvj6fJ38qNaT28Si7
ElYeYL3VoH7SKVeqFDHBjXsvo7KrcHGYpvBr1hY5CMunrCl2urcCy1P5adoUjkIW
ygvuEWAympeHnoGshwIDAQAB
-----END PUBLIC KEY-----
BBB;

    private $privateKey = <<<BBB
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDM7VUsLBFNFD2G8mOpRhmiA9x4BKqhW21P2h776wwqT2/OJqGc
BSF/EZJOK/MQqRSOqxNUYdwVvj6fJ38qNaT28Si7ElYeYL3VoH7SKVeqFDHBjXsv
o7KrcHGYpvBr1hY5CMunrCl2urcCy1P5adoUjkIWygvuEWAympeHnoGshwIDAQAB
AoGAGSvl1GEtVtxvmk3XtAkqSN5UjGF2XA+Q48gVGjiK6/+J6jaQj0uKC8OqxvNb
DebW4Zdd7nV+xSTzKDV/xz2Dn6FDP7Sx7OutjO9wSt+94ouGT3RY6qw52blWJQ1R
iGJ+hwQxCDD0uD0OeAkAMdcEb0FggQPAIeOLUyJi8hmVgrECQQDqS3W/cS1wYECx
28L4MXqLrDoldPnp94OvRhW6o5LONO6oxdNzxm+iMwIN231je3TOmy8/9yF3w+95
GXmvle8jAkEA3+ljWJEPzHqQo6yW+3HMxwQAzXVaxPjZ7lT3xyNC3U0Md43czpZA
2pug2C0nA29oCfKD7gci2vSFWaaeCie1TQJAYUwVCfumMxTFyRbKUOe7TGWpgASk
BFWViiRAwdFMFfZFZjFBLsMpeOJV6AtOdxG94E7xwE6Qx3vG5zN9JT3OoQJBAM1d
1VWtLu1f2PuV16DlvmkmncnUAh25FMFIwz2tdK1e9rlMryH3o6IdrYe42hiHfMfq
2+BcQTbLoOcaL8empd0CQAkHdcZ84Y1XWlVs6yTQGQbuQ8rvLaKJfYVUxO6cWWU0
cTqedHfsEmDPXYLVUFcaDVF1gsK1j+7zrfZbFexBBHE=
-----END RSA PRIVATE KEY-----
BBB;

    private $publicKey2 = <<<BBB
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzHhuPkhigHeYG0eOFjuX
nkMP1llge5NlTYxLyU1/jjuWnIgOyZKQHIdIyS2elnzGPFiEY60ywK4R8/erj/Er
rzY+J3UM4ppgZUuLM+o4i+kYImWX0/ebwjVYpllJMgGJHfcL7tgj1fNvj09WS7WU
y3TfciDl+Wj13oEnVRwEAiM8+gvqWgOsuu//JbecX73qn/ZPPyQoyylhucpk+y90
pg80rLlSCeh1odP5IbvGqSudOxemjJq1ChPMu5qOh+QFNPHWh5zrrDRYKdSU4tRq
FIvmOXSg2Gdp7oyKCxoiDxSJOgAAcPL0pWRyW4Zhhpfc0YU1mRw3LWfXVVgwnmN1
iQIDAQAB
-----END PUBLIC KEY-----
BBB;

    private $privateKey2 = <<<BBB
-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEAzHhuPkhigHeYG0eOFjuXnkMP1llge5NlTYxLyU1/jjuWnIgO
yZKQHIdIyS2elnzGPFiEY60ywK4R8/erj/ErrzY+J3UM4ppgZUuLM+o4i+kYImWX
0/ebwjVYpllJMgGJHfcL7tgj1fNvj09WS7WUy3TfciDl+Wj13oEnVRwEAiM8+gvq
WgOsuu//JbecX73qn/ZPPyQoyylhucpk+y90pg80rLlSCeh1odP5IbvGqSudOxem
jJq1ChPMu5qOh+QFNPHWh5zrrDRYKdSU4tRqFIvmOXSg2Gdp7oyKCxoiDxSJOgAA
cPL0pWRyW4Zhhpfc0YU1mRw3LWfXVVgwnmN1iQIDAQABAoIBAQCf98SAU89EpMxK
42OFf1/ygJL+ZvR2Ge4SiqWsO0aFN5dwpX20NEctGqZWRquhHsNU6QfCl/lyB32i
Om1t8wfzT2O3KPtIufCar0yb9C4DP/0SxBrRyhGBEo1lr8r1JYBqAiLC3TTEKW1p
WG+yUcC0oJ5EQvrJc1WQm8jy7DUymYpLTkj+wW5N7qnS7SLI5L2rHfpxMNGIHAs5
P4gac2E7pQ4f3iLLn2eEx7UZQ1IinW9TAll2QBMZa6ZOVnQsFO3PlnsNAxZ+lfvY
gqbuhjoYLlAeQkSxsBPqLXss87w2Qw4+NuF0J/ogXSePjv4PFEPIXWYACiCd1Y/x
47VGprg1AoGBAPSajNcTi0gZGqGvp6ymSNx7i9VHao4gh9Tfyz0bDBiuLOqOXs3N
4zswcZTfXeK+h95smuudQfRsSK29MR+3kOwN4cG+bKZre5AI1/9t8AkYiZepjhbr
1z370a2tS5l3tqgZx/zW7tJt0SvYwknqO5ySnvJTPRmuYXg9y2LjtPWnAoGBANX/
M2bUuGgSCJz81x0njsF7dWXnyps3UpTE0Ck3/Y4DBtBIXMUfhmnUVQa7lPMmcQcn
/cJGx4NC6g2nUTW49RkKSEr4nsfJ0F73YOTB9iAdZ4QeLkWPz8IMQa1ENCchV/ms
rEUc7/S9f3la0K+p47LS/g6Z0PO+8+3JSdA1egFPAoGAXLXXfA2kVQdu2KnDW+UK
6MbLEWOoN4aM9Vp9pgOCajhaPe0Icej/n4eVBWBELZUZ2mw/q95HCWWhhniXDfZ9
r3rzfoO2mr1ScB1qAR6iRFBQlnNlr7pkMtInfzSX2utNCBn9ew/cJVYKWhwmR+3H
+mh4ZlC2b+1wdCq31BuKkzECgYEAwFubus1vzayYLXVhcAWE3wq45pdKmedKxgt8
CfEYbDTwRP0m1tKVoj+JBnpLU520b/hUs/Onl6fod8l0yFOvjYienzWIlJImSZcY
c8ieExQbXrk6YrD40bbuum7aamogiH/cgmuWjmpgUZd+isitsqrSUBGXr+JvpckQ
HqZTOyUCgYEArE6eV+kUT/cjY+cbJhHx4VT/+gtPjxncDZayRjdxcHRBn1MLHqHQ
Db/hHAmtLkR2HWA9DRPhFxflrffBNtJnlOtmA3KL/5a2AdP6hROM8SMe1+DKrOtR
3JiszMbraILeDMuUD5tk8lwd487gYSxKWAO1NZjGvsum376kn5rUKYM=
-----END RSA PRIVATE KEY-----
BBB;
    
    public function testRsaObject(){
        $content = file_get_contents(__FILE__);
        $object = "encry.txt";
        $keys = array(
            'public_key' => $this->publicKey,
            'private_key' => $this->privateKey
        );
        $matDesc= array(
            'key1'=>'test-one'
        );
        $provider= new RsaProvider($keys,$matDesc);
        $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object,$content);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
           $this->assertTrue(false);
        }

        try {
            $result = $this->ossEncryptionClient->getObject($this->bucket,$object);
            $this->assertEquals($result,$content);
        }catch (OssException $e){
            $this->assertTrue(false);
        }


        $content2 = "Hi,hello This is a test";
        $object2 = "encry2.txt";

        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object2,$content2);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            $result2 = $this->ossEncryptionClient->getObject($this->bucket,$object2);
            $this->assertEquals($result2,$content2);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object2,$content2);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            $result2 = $this->ossEncryptionClient->getObject($this->bucket,$object2);
            $this->assertEquals($result2,$content2);
        }catch (OssException $e){
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $keys = array(
                'public_key' => $this->publicKey2,
                'private_key' => $this->privateKey2
            );
            $matDesc= array(
                'key2'=>'test-two'
            );
            $provider= new RsaProvider($keys,$matDesc);
            $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object2,$content2);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $keys2 = array(
                'public_key' => $this->publicKey2,
                'private_key' => $this->privateKey2
            );
            $matDesc2= array(
                'key2'=>'test-two'
            );
            $encryptionMaterials = new RsaEncryptionMaterials($matDesc2,$keys2);
            $provider->addEncryptionMaterials($encryptionMaterials);
            $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
            $result = $this->ossEncryptionClient->getObject($this->bucket,$object2);
            $this->assertEquals($result,$content2);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

    }

    public function testRsaMultiUploadAndDownload(){
        ini_set('memory_limit', '8G');
        try {
            print_r( "testRsaMultiUploadAndDownload Begin".PHP_EOL);
            $object = "multi-upload.rar";
            $keys = array(
                'public_key' => $this->publicKey,
                'private_key' => $this->privateKey
            );
            $matDesc= array(
                'key1'=>'test-one'
            );
            $provider= new RsaProvider($keys,$matDesc);
            $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
            $partSize = 100 * 1024;
            $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
            if (file_exists($bigFileName)){
                unlink($bigFileName);
            }
            OssUtil::generateFile($bigFileName, 300 * 1024);
            $uploadFile = $bigFileName;
            $uploadFileSize = sprintf('%u',filesize($uploadFile));
            $options['headers'] = array(
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_DATA_SIZE => $uploadFileSize,
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_PART_SIZE=>$partSize
            );
            $uploadId = $this->ossEncryptionClient->initiateMultipartUpload($this->bucket, $object,$options);
            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
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
                $responseUploadPart[] = $this->ossEncryptionClient->uploadPart($this->bucket, $object, $uploadId, $upOptions);
                printf( "initiateMultipartUpload, uploadPart - part#{$i} OK\n");
            }
            $uploadParts = array();
            foreach ($responseUploadPart as $i => $eTag) {
                $uploadParts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }
            $this->ossEncryptionClient->completeMultipartUpload($this->bucket, $object, $uploadId, $uploadParts);
            printf("completeMultipartUpload OK\n");
        }catch (OssException $e) {
            print_r($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        try {
            $download = 'download.tmp';
            $objectMeta = $this->ossEncryptionClient->getObjectMeta($this->bucket, $object);
            $size = $objectMeta['content-length'];
            $partSize =1024*100;
            $pieces2 = $this->ossEncryptionClient->generateMultiuploadParts($size, $partSize);
            $downloadPosition = 0;
            if (file_exists($download)){
                unlink($download);
            }
            foreach ($pieces2 as $i => $piece2) {
                $fromPos2 = $downloadPosition + (integer)$piece2[OssClient::OSS_SEEK_TO];
                $toPos2 = (integer)$piece2[OssClient::OSS_LENGTH] + $fromPos2 - 1;
                $downOptions = array(
                    OssClient::OSS_RANGE => $fromPos2.'-'.$toPos2
                );
                $content2 = $this->ossEncryptionClient->getObject($this->bucket,$object,$downOptions);
                file_put_contents($download, $content2,FILE_APPEND );
                printf("Multi download, part - part#{$i} OK\n");
            }
            $this->assertEquals(md5_file($uploadFile),md5_file($download));
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }
        unlink($bigFileName);
        unlink($download);
    }

    public function testResumeUploadAndDownload(){
        ini_set('memory_limit', '8G');
        $object = "multi-upload.rar";
        $keys = array(
            'public_key' => $this->publicKey,
            'private_key' => $this->privateKey
        );
        $matDesc= array(
            'key1'=>'test-one'
        );
        $provider= new RsaProvider($keys,$matDesc);
        $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
        try{
            $partSize =  100 * 1024;
            $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
            if (file_exists($bigFileName)){
                unlink($bigFileName);
            }
            OssUtil::generateFile($bigFileName, 120 * 1024);
            $uploadFile = $bigFileName;
            $uploadFileSize = sprintf('%u',filesize($uploadFile));
            $options['headers'] = array(
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_DATA_SIZE => $uploadFileSize,
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_PART_SIZE=>$partSize
            );
            $uploadId = $this->ossEncryptionClient->initiateMultipartUpload($this->bucket, $object,$options);
            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
            $responseUploadPart = array();
            $uploadPosition = 0;
            $uploadInfo = array(
                'uploadId' =>$uploadId,
                'object'=>$object,
                'uploadFile'=>$uploadFile,
                'partSize'=>$partSize,
            );
            foreach ($pieces as $i => $piece) {
                $fromPos = $uploadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
                $content = OssUtil::getDataFromFile($uploadFile,$fromPos,$toPos);
                $upOptions = array(
                    OssClient::OSS_PART_NUM => ($i + 1),
                    OssClient::OSS_CONTENT => $content,
                );
                $responseUploadPart[] = $this->ossEncryptionClient->uploadPart($this->bucket, $object, $uploadId, $upOptions);
                $uploadInfo['parts'] = $responseUploadPart;
                file_put_contents('upload.ucp',json_encode($uploadInfo));
                if ($i == 2){
                    break;
                }
            }
        }catch (OssException $e) {
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }
        try{
            $str = file_get_contents("upload.ucp");
            $uploadInfo = json_decode($str,true);
            $uploadId = $uploadInfo['uploadId'];
            $parts = $uploadInfo['parts'];
            $object = $uploadInfo['object'];
            $partSize = $uploadInfo['partSize'];
            $uploadFile = $uploadInfo['uploadFile'];
            $uploadFileSize = sprintf('%u',filesize($uploadFile));
            $num = count($parts);
            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
            $responseUploadPart = array();
            $uploadPosition = 0;
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
                $responseUploadPart[] = $this->ossEncryptionClient->uploadPart($this->bucket, $object, $uploadId, $upOptions);
            }
            $responseUploadPart = array_merge($parts,$responseUploadPart);
            foreach ($responseUploadPart as $i => $eTag) {
                $uploadParts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }
            $this->ossEncryptionClient->completeMultipartUpload($this->bucket, $object, $uploadId, $uploadParts);
        }catch (OssException $e) {
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }
        try {
            $download = "download.tmp";
            if (file_exists($download)){
                unlink($download);
            }
            $objectMeta = $this->ossEncryptionClient->getObjectMeta($this->bucket, $object);
            $size = $objectMeta['content-length'];

            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($size, $partSize);
            $downloadPosition = 0;
            $downloadArray = array(
                "object" => $object,
                "pieces" => $pieces,
            );
            foreach ($pieces as $i => $piece) {
                $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
                $downOptions = array(
                    OssClient::OSS_RANGE => $fromPos.'-'.$toPos
                );
                $content = $this->ossEncryptionClient->getObject($this->bucket,$object,$downOptions);
                file_put_contents($download, $content,FILE_APPEND );
                $downloadArray['parts'] = $i+1;

                if ($i == 2){
                    break;
                }
            }
            file_put_contents("download.ucp",json_encode($downloadArray));
            printf( "Object ".$object.' download complete');
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        try {
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
                $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
                $downOptions = array(
                    OssClient::OSS_RANGE => $fromPos.'-'.$toPos
                );
                $content = $this->ossEncryptionClient->getObject($this->bucket,$object,$downOptions);
                file_put_contents($download, $content,FILE_APPEND );
            }
            $this->assertEquals(md5_file($download),md5_file($uploadFile));
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        try {
            if (file_exists($download)){
                unlink($download);
            }
            $result = $this->ossEncryptionClient->getObject($this->bucket, $object);
            file_put_contents($download, $result);
            $this->assertEquals(md5_file($uploadFile),md5_file($download));
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        unlink($bigFileName);
        unlink($download);
        unlink("download.ucp");
        unlink('upload.ucp');

    }

    public function testRsaRangeDownload(){
        $content = file_get_contents(__FILE__);
        $object = "encrypt.txt";
        $keys = array(
            'public_key' => $this->publicKey,
            'private_key' => $this->privateKey
        );
        $matDesc= array(
            'key1'=>'test-one'
        );
        $provider= new RsaProvider($keys,$matDesc);
        $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object,$content);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            $this->assertTrue(false);
        }
        try {
            $options = array(
                OssClient::OSS_RANGE => '48-100'
            );
            $result = $this->ossEncryptionClient->getObject($this->bucket,$object,$options);
            $this->assertEquals($result,OssUtil::getDataFromFile(__FILE__,48,100));
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            $options = array(
                OssClient::OSS_RANGE => '13-100'
            );
            $result = $this->ossEncryptionClient->getObject($this->bucket,$object,$options);
            $this->assertEquals($result,OssUtil::getDataFromFile(__FILE__,13,100));
        }catch (OssException $e){
            $this->assertTrue(false);
        }
    }

    public function testKmsObject(){

        $content = file_get_contents(__FILE__);
        $object = "kms-encrypt.txt";
        $matDesc= array(
            'key2'=>'test-kms'
        );
        $cmkId= Common::getKmsId();
        $provider= new KmsProvider(Common::getAccessKeyId(),Common::getAccessKeySecret(),Common::getKmsEndPoint(),$cmkId,$matDesc);
        $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object,$content);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }
        try {
            $result = $this->ossEncryptionClient->getObject($this->bucket,$object);
            $this->assertEquals($result,$content);
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }


        $content2 = "Hi,hello This is a test";
        $object2 = "kms-encrypt2.txt";
        $matDesc= array(
            'key1'=>'test-kms-two'
        );
        $provider= new KmsProvider(Common::getAccessKeyId(),Common::getAccessKeySecret(),Common::getKmsEndPointOther(),$cmkId,$matDesc);
        $otherKmsRegion = Common::getKmsEndPoint();
        $matDesc2= array(
            'key2'=>'test-kms'
        );
        $kmsId2 = Common::getKmsIdOther();
        $encryptionMaterials = new KmsEncryptionMaterials($matDesc2,$otherKmsRegion,$kmsId2);
        $provider->addEncryptionMaterials($encryptionMaterials);
        $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);

        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object2,$content2);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            $this->assertTrue(false);
        }
        try {
            $result = $this->ossEncryptionClient->getObject($this->bucket,$object);
            $this->assertEquals($result,$content);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            $result = $this->ossEncryptionClient->putObject($this->bucket,$object2,$content2);
            $this->assertNotNull($result['oss-requestheaders']['x-oss-meta-client-side-encryption-cek-alg']);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

        try {
            $result2 = $this->ossEncryptionClient->getObject($this->bucket,$object2);
            $this->assertEquals($result2,$content2);
        }catch (OssException $e){
            $this->assertTrue(false);
        }

    }

    public function testKmsMultiUploadAndDownload(){
        $object = "multi-upload.rar";
        try {
            $matDesc= array(
                'key1'=>'test-kms-two'
            );
            $cmkId= Common::getKmsId();
            $provider= new KmsProvider(Common::getAccessKeyId(),Common::getAccessKeySecret(),Common::getKmsEndPointOther(),$cmkId,$matDesc);
            $otherKmsRegion = Common::getKmsEndPoint();
            $matDesc2= array(
                'key2'=>'test-kms'
            );
            $kmsId2 = Common::getKmsIdOther();
            $encryptionMaterials = new KmsEncryptionMaterials($matDesc2,$otherKmsRegion,$kmsId2);
            $provider->addEncryptionMaterials($encryptionMaterials);
            $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
            $partSize = 100 * 1024;
            $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
            if (file_exists($bigFileName)){
                unlink($bigFileName);
            }
            OssUtil::generateFile($bigFileName, 300 * 1024);
            $uploadFile = $bigFileName;
            $uploadFileSize = sprintf('%u',filesize($uploadFile));
            $options['headers'] = array(
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_DATA_SIZE => $uploadFileSize,
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_PART_SIZE=>$partSize
            );
            $uploadId = $this->ossEncryptionClient->initiateMultipartUpload($this->bucket, $object,$options);
            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
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
                $responseUploadPart[] = $this->ossEncryptionClient->uploadPart($this->bucket, $object, $uploadId, $upOptions);
                printf( "initiateMultipartUpload, uploadPart - part#{$i} OK\n");
            }
            $uploadParts = array();
            foreach ($responseUploadPart as $i => $eTag) {
                $uploadParts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }
            $this->ossEncryptionClient->completeMultipartUpload($this->bucket, $object, $uploadId, $uploadParts);
            printf("completeMultipartUpload OK\n");
        }catch (OssException $e) {
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }


        try {
            $download = 'download.rar';
            $objectMeta = $this->ossEncryptionClient->getObjectMeta($this->bucket, $object);
            $size = $objectMeta['content-length'];
            $partSize =1024*100;
            $pieces2 = $this->ossEncryptionClient->generateMultiuploadParts($size, $partSize);
            $downloadPosition = 0;
            if (file_exists($download)){
                unlink($download);
            }
            foreach ($pieces2 as $i => $piece2) {
                $fromPos2 = $downloadPosition + (integer)$piece2[OssClient::OSS_SEEK_TO];
                $toPos2 = (integer)$piece2[OssClient::OSS_LENGTH] + $fromPos2 - 1;
                $downOptions = array(
                    OssClient::OSS_RANGE => $fromPos2.'-'.$toPos2
                );
                $content2 = $this->ossEncryptionClient->getObject($this->bucket,$object,$downOptions);
                file_put_contents($download, $content2,FILE_APPEND );
                printf("Multi download, part - part#{$i} OK\n");
            }
            $this->assertEquals(md5_file($uploadFile),md5_file($download));
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }
    }

    public function testKmsResumeUploadAndDownload(){
        $object = "multi-upload.rar";
        $matDesc= array(
            'key1'=>'test-kms-two'
        );
        $cmkId= Common::getKmsId();
        $provider= new KmsProvider(Common::getAccessKeyId(),Common::getAccessKeySecret(),Common::getKmsEndPointOther(),$cmkId,$matDesc);
        $otherKmsRegion = Common::getKmsEndPoint();
        $matDesc2= array(
            'key2'=>'test-kms'
        );
        $cmkId2 = Common::getKmsIdOther();
        $encryptionMaterials = new KmsEncryptionMaterials($matDesc2,$otherKmsRegion,$cmkId2);
        $provider->addEncryptionMaterials($encryptionMaterials);
        $this->ossEncryptionClient = Common::getOssEncryptionClient($provider);
        try{
            $partSize = 100 * 1024;
            $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
            if (file_exists($bigFileName)){
                unlink($bigFileName);
            }
            OssUtil::generateFile($bigFileName, 300 * 1024);
            $uploadFile = $bigFileName;
            $uploadFileSize = sprintf('%u',filesize($uploadFile));
            $options['headers'] = array(
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_DATA_SIZE => $uploadFileSize,
                OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_PART_SIZE=>$partSize
            );
            $uploadId = $this->ossEncryptionClient->initiateMultipartUpload($this->bucket, $object,$options);
            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
            $responseUploadPart = array();
            $uploadPosition = 0;
            $uploadInfo = array(
                'uploadId' =>$uploadId,
                'object'=>$object,
                'uploadFile'=>$uploadFile,
                'partSize'=>$partSize,
            );
            foreach ($pieces as $i => $piece) {
                $fromPos = $uploadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
                $content = OssUtil::getDataFromFile($uploadFile,$fromPos,$toPos);
                $upOptions = array(
                    OssClient::OSS_PART_NUM => ($i + 1),
                    OssClient::OSS_CONTENT => $content,
                );
                $responseUploadPart[] = $this->ossEncryptionClient->uploadPart($this->bucket, $object, $uploadId, $upOptions);
                $uploadInfo['parts'] = $responseUploadPart;
                file_put_contents('upload.ucp',json_encode($uploadInfo));
                if ($i == 2){
                    break;
                }
            }
        }catch (OssException $e) {
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        try{
            $str = file_get_contents("upload.ucp");
            $uploadInfo = json_decode($str,true);
            $uploadId = $uploadInfo['uploadId'];
            $parts = $uploadInfo['parts'];
            $object = $uploadInfo['object'];
            $partSize = $uploadInfo['partSize'];
            $uploadFile = $uploadInfo['uploadFile'];
            $uploadFileSize = sprintf('%u',filesize($uploadFile));
            $num = count($parts);
            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($uploadFileSize, $partSize);
            $responseUploadPart = array();
            $uploadPosition = 0;
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
                $responseUploadPart[] = $this->ossEncryptionClient->uploadPart($this->bucket, $object, $uploadId, $upOptions);
            }
            $responseUploadPart = array_merge($parts,$responseUploadPart);
            foreach ($responseUploadPart as $i => $eTag) {
                $uploadParts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }
            $this->ossEncryptionClient->completeMultipartUpload($this->bucket, $object, $uploadId, $uploadParts);
        }catch (OssException $e) {
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        try {
            $download = "demo.rar";
            $objectMeta = $this->ossEncryptionClient->getObjectMeta($this->bucket, $object);
            $size = $objectMeta['content-length'];
            $partSize =1024*100;

            $pieces = $this->ossEncryptionClient->generateMultiuploadParts($size, $partSize);
            $downloadPosition = 0;
            $downloadArray = array(
                "object" => $object,
                "pieces" => $pieces,
            );
            foreach ($pieces as $i => $piece) {
                $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
                $downOptions = array(
                    OssClient::OSS_RANGE => $fromPos.'-'.$toPos
                );
                $content = $this->ossEncryptionClient->getObject($this->bucket,$object,$downOptions);
                file_put_contents($download, $content,FILE_APPEND );
                $downloadArray['parts'] = $i+1;

                if ($i == 2){
                    break;
                }
            }

            file_put_contents("download.ucp",json_encode($downloadArray));
            printf( "Object ".$object.'download complete');
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }


        try {
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
                $fromPos = $downloadPosition + (integer)$piece[OssClient::OSS_SEEK_TO];
                $toPos = (integer)$piece[OssClient::OSS_LENGTH] + $fromPos - 1;
                $downOptions = array(
                    OssClient::OSS_RANGE => $fromPos.'-'.$toPos
                );
                $content = $this->ossEncryptionClient->getObject($this->bucket,$object,$downOptions);
                file_put_contents($download, $content,FILE_APPEND );
            }
            $this->assertEquals(md5_file($download),md5_file($uploadFile));
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        try {
            if (file_exists($download)){
                unlink($download);
            }
            $result = $this->ossEncryptionClient->getObject($this->bucket, $object);
            file_put_contents($download, $result);
            $this->assertEquals(md5_file($uploadFile),md5_file($download));
        }catch (OssException $e){
            printf($e->getMessage() . PHP_EOL);
            $this->assertTrue(false);
        }

        unlink($bigFileName);
        unlink($download);
        unlink("download.ucp");
        unlink('upload.ucp');
    }
}


