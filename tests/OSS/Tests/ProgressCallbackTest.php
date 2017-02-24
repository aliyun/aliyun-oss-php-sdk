<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';

class ProgressCallbackTest extends TestOssClientBase
{
    /**
     * 上传本地文件到oss
     */
    public function testUploadFileProcessCallback()
    {
        $object = "progress_callback_file";
        $this->runCmd('dd if=/dev/zero of=' . $this->local_file . ' bs=1M count=4');
        $this->total_bytes = 1024 * 1024 * 4;
        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                );
        try {
            $result = $this->ossClient->uploadFile($this->bucket, $object, $this->local_file, $options);
        } catch (OssException $e) {
            $this->runCmd('rm' .  $this->local_file);
            $this->assertFalse(true);
        }
    }

    /**
     * 分片上传到oss
     */
    public function testMultiUploadProcessCallback()
    {
        $object = "progress_callback_file";
        $this->runCmd('dd if=/dev/zero of=' . $this->local_file . ' bs=1M count=64');
        $this->total_bytes = 1024 * 1024 * 64;
        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                );
        try {
            $result = $this->ossClient->multiuploadFile($this->bucket, $object, $this->local_file, $options);
        } catch (OssException $e) {
            $this->runCmd('rm' .  $this->local_file);
            $this->assertFalse(true);
        }

    }
 
    /**
     * 追加本地文件到oss
     */
   public function testAppendFileCallback()
    {
        $object = "progress_callback_file";
        $this->runCmd('dd if=/dev/zero of=' . $this->local_file . ' bs=1M count=4');
        $this->total_bytes = 1024 * 1024 * 4;
        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                );
        try {
            $result = $this->ossClient->appendFile($this->bucket, $object, $this->local_file, 0, $options);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }
   
    /**
     * 上传临时变量到oss
     */
    public function testPutObjectCallback()
    {
        $content = "this is for test progress callback.";
        $object = "progress_callback_file";
        $this->total_bytes = strlen($content);
        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                   );
        try {
            $result = $this->ossClient->putObject($this->bucket, $object, $content, $options);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }
   
    /**
     * 追加临时变量到oss
     */
   public function testAppendObjectCallback()
   {
        $content = "this is for test progress callback.";
        $object = "progress_callback_file";
        $this->total_bytes = strlen($content);

        $content_array = array($content);
        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                );
        try {
            $result = $this->ossClient->appendObject($this->bucket, $object, $content_array[0], 0, $options);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }
   
    /**
     * 下载oss文件到本地
     */
    public function testGetObjectCallback()
    {
        $object = "progress_callback_file";
        $this->runCmd('dd if=/dev/zero of=' . $this->local_file . ' bs=1M count=4');
        try {
            $result = $this->ossClient->uploadFile($this->bucket, $object, $this->local_file);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $this->total_bytes = filesize($this->local_file);
        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                        OssClient::OSS_FILE_DOWNLOAD => "progress_test_file_local",
                );
        try {
            $result = $this->ossClient->getObject($this->bucket, $object, $options);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    /**
     * 下载oss文件到临时变量
     */
   public function testGetObjectToContentCallback()
   {
        $content = "this is for test progress callback.";
        $object = "progress_callback_file";
        $this->total_bytes = strlen($content);
        
        try {
            $result = $this->ossClient->putObject($this->bucket, $object, $content);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $options = array(
                        OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
                );
        try {
            $result = $this->ossClient->getObject($this->bucket, $object, $options);
            $this->assertEquals($content, $result);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }
    /**
     * Range get oss文件
     */
    public function testRangeGetObjectCallback()
    {
        $content = "this is for test progress callback.";
        $object = "progress_callback_file";
        
        try {
            $result = $this->ossClient->putObject($this->bucket, $object, $content);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $options = array(OssClient::OSS_RANGE => '0-11',
                         OssClient::OSS_PROGRESS_CALLBACK => array($this, "progress_callback"),
        );
        $this->total_bytes = 12;

        try {
            $content = $this->ossClient->getObject($this->bucket, $object, $options);
            $this->assertEquals(strlen($content), 12);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }
    private function runCmd($cmd)
    {
        $output = array();
        $status = 0;
        exec($cmd . ' 2>/dev/null', $output, $status);

        $this->assertEquals(0, $status);
    }

    public function progress_callback($consumed, $total)
    {        
        $this->assertEquals($this->total_bytes, $total);
    }

    public function setUp()
    {
        parent::setUp();
    }

    private $local_file = "progress_callback_test";
    private $total_bytes = 0;
}
