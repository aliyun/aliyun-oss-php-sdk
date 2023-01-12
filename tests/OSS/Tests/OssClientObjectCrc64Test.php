<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Core\OssUtil;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientObjectCrc64Test extends TestOssClientBase
{

    public function testObject()
    {
        /**
         *  Upload the local variable to bucket
         */
        $object = "oss-php-sdk-test/upload-test-object-name.txt";
        $content = file_get_contents(__FILE__);
        $options = array(
            OssClient::OSS_CHECK_CRC64=>true
        );

        try {
            $this->ossClient->putObject($this->bucket . 'not_exist', $object, $content, $options);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertFalse(false);
        }

        try {
            $this->ossClient->putObject($this->bucket, $object, $content, $options);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $content = $this->ossClient->getObject($this->bucket, $object,$options);
            $this->assertEquals($content, file_get_contents(__FILE__));
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $options[OssClient::OSS_RANGE] = "0-29";
        try {
            $this->ossClient->getObject($this->bucket, $object, $options);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

    }

    public function testAppendObject()
    {
        $object = "oss-php-sdk-test/append-test-object-name.txt";
        $content_array = array('Hello OSS', 'Hi OSS', 'OSS OK');
        
        /**
         * Append the upload string
         */
        $options = array(
            OssClient::OSS_CHECK_CRC64=>true
        );
        try {
            $result = $this->ossClient->appendObject($this->bucket, $object, $content_array[0], 0,$options);
            $this->assertEquals($result->getPosition(), strlen($content_array[0]));
            $options[OssClient::OSS_INIT_CRC64] = $result->getCrc();
            $result1 = $this->ossClient->appendObject($this->bucket, $object, $content_array[1], $result->getPosition(),$options);
            $this->assertEquals($result1->getPosition(), strlen($content_array[0]) + strlen($content_array[1]));
            $options[OssClient::OSS_INIT_CRC64] = $result1->getCrc();
            $result2 = $this->ossClient->appendObject($this->bucket, $object, $content_array[2], $result1->getPosition(), $options);
            $this->assertEquals($result2->getPosition(), strlen($content_array[0]) + strlen($content_array[1]) + strlen($content_array[2]));
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertFalse(true);
        }


        /**
         * Check if the content is the same
         */
        try {
            $content = $this->ossClient->getObject($this->bucket, $object);
            $this->assertEquals($content, implode($content_array));
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        /**
         * Delete test object
         */
        try {
            $this->ossClient->deleteObject($this->bucket, $object);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
        /**
         * Append the upload of local files
         */
        $options = array(
            OssClient::OSS_CHECK_CRC64=>true
        );
        try {
            $rs = $this->ossClient->appendFile($this->bucket, $object, __FILE__, 0,$options);
            $this->assertEquals($rs->getPosition(), filesize(__FILE__));
            $options[OssClient::OSS_INIT_CRC64] = $rs->getCrc();
            $rs1 = $this->ossClient->appendFile($this->bucket, $object, __FILE__, $rs->getPosition(),$options);
            $this->assertEquals($rs1->getPosition(), filesize(__FILE__) * 2);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertFalse(true);
        }

        try {
            $content = $this->ossClient->getObject($this->bucket, $object);
            $this->assertEquals($content, file_get_contents(__FILE__) . file_get_contents(__FILE__));
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertFalse(true);
        }
        
        /**
         * Delete test object
         */
        try {
            $this->ossClient->deleteObject($this->bucket, $object);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    public function testPutObjectByRawApisWithCrc64Check()
    {
        $object = "mpu/multipart-test.txt";
        try {
            $upload_id = $this->ossClient->initiateMultipartUpload($this->bucket, $object);
            $part_size = 10 * 1024 * 1024;
            $upload_file = __FILE__;
            $upload_filesize = filesize($upload_file);
            $pieces = $this->ossClient->generateMultiuploadParts($upload_filesize, $part_size);
            var_dump($pieces);
            $response_upload_part = array();
            $upload_position = 0;
            $is_check_crc64 = true;
            foreach ($pieces as $i => $piece) {
                $from_pos = $upload_position + (integer)$piece[OssClient::OSS_SEEK_TO];
                $to_pos = (integer)$piece[OssClient::OSS_LENGTH] + $from_pos - 1;
                $up_options = array(
                    OssClient::OSS_FILE_UPLOAD => $upload_file,
                    OssClient::OSS_PART_NUM => ($i + 1),
                    OssClient::OSS_SEEK_TO => $from_pos,
                    OssClient::OSS_LENGTH => $to_pos - $from_pos + 1,
                    OssClient::OSS_CHECK_CRC64 => $is_check_crc64,
                );
                try {
                    $response_upload_part[] = $this->ossClient->uploadPart($this->bucket, $object, $upload_id, $up_options);
                } catch (OssException $e) {
                    $this->assertFalse(true);
                }
            }
            $upload_parts = array();
            foreach ($response_upload_part as $i => $eTag) {
                $upload_parts[] = array(
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                );
            }
        } catch (OssException $e) {
            $this->assertFalse(true);
        }


        try {
            $listPartsInfo = $this->ossClient->listParts($this->bucket, $object, $upload_id);
            $this->assertNotNull($listPartsInfo);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->completeMultipartUpload($this->bucket, $object, $upload_id, $upload_parts);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }
    }

    public function testPutObjectByMultipartUploadWithCrc64Check()
    {
        $object = "mpu/multipart-test.txt";
        $file = __FILE__;
        $options = array(OssClient::OSS_CHECK_CRC64 => true);

        try {
            $this->ossClient->multiuploadFile($this->bucket, $object, $file, $options);
            $this->assertFalse(false);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    public function testMultipartUploadBigFileWithCrc64Check()
    {
        $bigFileName = __DIR__ . DIRECTORY_SEPARATOR . "/bigfile.tmp";
        $localFilename = __DIR__ . DIRECTORY_SEPARATOR . "/localfile.tmp";
        if (file_exists($bigFileName)){
            unlink($bigFileName);
        }
        if (file_exists($localFilename)){
            unlink($localFilename);
        }
        OssUtil::generateFile($bigFileName, 6 * 1024 * 1024);
        $object = 'mpu/multipart-bigfile-test.tmp';
        $options = array(
            OssClient::OSS_CHECK_CRC64 => true,
            OssClient::OSS_PART_SIZE => 1024*1024,
        );
        try {
            $this->ossClient->multiuploadFile($this->bucket, $object, $bigFileName, $options);
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

}
