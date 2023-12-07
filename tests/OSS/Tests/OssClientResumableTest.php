<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\OssClient;
use OSS\Core\OssUtil;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientResumableTest extends TestOssClientBase
{

    public function testResumableUploadBigFile()
    {
        $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
        $localFilename = __DIR__ . DIRECTORY_SEPARATOR . "/localfile.tmp";
        OssUtil::generateFile($bigFileName, 6 * 1024 * 1024);
        $object = 'mpu/multipart-bigfile-test.tmp';
        try {
            $this->ossClient->resumableUpload($this->bucket, $object, $bigFileName, array(OssClient::OSS_PART_SIZE => 1));
            $options = array(OssClient::OSS_FILE_DOWNLOAD => $localFilename);
            $this->ossClient->getObject($this->bucket, $object, $options);
            $this->assertEquals(md5_file($bigFileName), md5_file($localFilename));
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertFalse(true);
        }
        unlink($bigFileName);
        unlink($localFilename);
    }


    public function testResumableUpload()
    {
        $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
        $localFilename = __DIR__ . DIRECTORY_SEPARATOR . "/localfile.tmp";
        OssUtil::generateFile($bigFileName, 6 * 1024 * 1024);
        $object = 'mpu/multipart-bigfile-test.tmp';
        try {
            $options = array(
                OssClient::OSS_PART_SIZE => 1024*1024,
                OssClient::OSS_CHECK_MD5 => true,
                "uploadPartHooker"=>3
            );
            $this->ossClient->resumableUpload($this->bucket, $object, $bigFileName, $options);

        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $options = array(
                OssClient::OSS_PART_SIZE => 1024*1024,
                OssClient::OSS_CHECK_MD5 => true,
                "uploadPartHooker"=>5
            );
            $this->ossClient->resumableUpload($this->bucket, $object, $bigFileName, $options);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $options = array(
                OssClient::OSS_PART_SIZE => 1024*1024,
                OssClient::OSS_CHECK_MD5 => true,
            );
            $this->ossClient->resumableUpload($this->bucket, $object, $bigFileName, $options);
            $options = array(OssClient::OSS_FILE_DOWNLOAD => $localFilename);
            $this->ossClient->getObject($this->bucket, $object, $options);
            $this->assertEquals(md5_file($bigFileName), md5_file($localFilename));
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertFalse(true);
        }
        unlink($bigFileName);
        unlink($localFilename);
    }


    public function testResumableDownload()
    {
        $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
        $localfile = __DIR__ . DIRECTORY_SEPARATOR . "/localfile.tmp";
        OssUtil::generateFile($bigFileName, 6 * 1024 * 1024);
        $object = 'mpu/multipart-bigfile-test.tmp';
        try {
            $this->ossClient->resumableUpload($this->bucket, $object, $bigFileName);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $options = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile,
                OssClient::OSS_PART_SIZE => 1024*1024,
                "uploadPartHooker"=>3
            );
            $this->ossClient->resumableDownload($this->bucket, $object, $options);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $options = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile,
                OssClient::OSS_PART_SIZE => 1024*1024,
                "uploadPartHooker"=>5
            );
            $this->ossClient->resumableDownload($this->bucket, $object, $options);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $options = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile,
                OssClient::OSS_PART_SIZE => 1024*1024,
            );
            $this->ossClient->resumableDownload($this->bucket, $object, $options);
            $this->assertEquals(md5_file($bigFileName), md5_file($localfile));
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertFalse(true);
        }

        unlink($bigFileName);
        unlink($localfile);
    }


    public function testResumableDownloadWithVersionId()
    {
        $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
        $localfile = __DIR__ . DIRECTORY_SEPARATOR . "/localfile.tmp";
        $localfile2 = __DIR__ . DIRECTORY_SEPARATOR . "/localfile2.tmp";
        OssUtil::generateFile($bigFileName, 6 * 1024 * 1024);
        $object = 'mpu/multipart-bigfile-test.tmp';
        $this->ossClient->putBucketVersioning($this->bucket, "Enabled");
        try {
            $result = $this->ossClient->putObject($this->bucket, $object, $bigFileName);
            $versionId = $result[OssClient::OSS_HEADER_VERSION_ID];
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $options = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile2,
                OssClient::OSS_VERSION_ID=>$versionId,
            );
            $this->ossClient->getObject($this->bucket, $object, $options);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $options2 = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile,
                OssClient::OSS_PART_SIZE => 1024*1024,
                OssClient::OSS_VERSION_ID=>$versionId,
                "uploadPartHooker"=>3
            );
            $this->ossClient->resumableDownload($this->bucket, $object, $options);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $options2 = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile,
                OssClient::OSS_PART_SIZE => 1024*1024,
                OssClient::OSS_VERSION_ID=>$versionId,
            );
            $this->ossClient->resumableDownload($this->bucket, $object, $options2);
            $this->assertEquals(md5_file($localfile2), md5_file($localfile));
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertFalse(true);
        }



        unlink($bigFileName);
        unlink($localfile);
        unlink($localfile2);
    }

    protected function tearDown(): void
    {
        if (!$this->ossClient->doesBucketExist($this->bucket)) {
            return;
        }

        $this->ossClient->putBucketVersioning($this->bucket, "Suspended");

        $result = $this->ossClient->listObjectVersions(
            $this->bucket, array('max-keys' => 1000, 'delimiter' => ''));

        $versions = $result->getObjectVersionList();
        $deleteMarkers = $result->getDeleteMarkerList();

        foreach ($versions as $obj) {
            $options = array(
                OssClient::OSS_VERSION_ID => $obj->getVersionId(),
            );
            $this->ossClient->deleteObject($this->bucket, $obj->getKey(), $options);
        }

        foreach ($deleteMarkers as $del) {
            $options = array(
                OssClient::OSS_VERSION_ID => $del->getVersionId(),
            );
            $this->ossClient->deleteObject($this->bucket, $del->getKey(), $options);
        }

        parent::tearDown();
    }

}
