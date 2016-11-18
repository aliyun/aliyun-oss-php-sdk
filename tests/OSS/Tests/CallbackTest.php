<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class CallbackTest extends TestOssClientBase
{
    public function testMultipartUploadCallbackNormal()
    {
        $object = "multipart-callback-test.txt";
        $copiedObject = "multipart-callback-test.txt.copied";
        $this->ossClient->putObject($this->bucket, $copiedObject, file_get_contents(__FILE__));

        /**
         *  step 1. 初始化一个分块上传事件, 也就是初始化上传Multipart, 获取upload id
         */
        try {
            $upload_id = $this->ossClient->initiateMultipartUpload($this->bucket, $object);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
        /*
         * step 2. uploadPartCopy
         */
        $copyId = 1;
        $eTag = $this->ossClient->uploadPartCopy($this->bucket, $copiedObject, $this->bucket, $object, $copyId, $upload_id);
        $upload_parts[] = array(
            'PartNumber' => $copyId,
            'ETag' => $eTag,
        );

        try {
            $listPartsInfo = $this->ossClient->listParts($this->bucket, $object, $upload_id);
            $this->assertNotNull($listPartsInfo);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        /**
         * step 3.
         */
        
        $json = 
        '{
            "callbackUrl":"callback.oss-demo.com:23450",
            "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
            "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
            "callbackBodyType":"application/json"
        }';
            
       $var = 
       '{
           "x:var1":"value1",
           "x:var2":"值2"
       }';
       $options = array(OssClient::OSS_CALLBACK => $json,
                        OssClient::OSS_CALLBACK_VAR => $var
                       );

        try {
            $result = $this->ossClient->completeMultipartUpload($this->bucket, $object, $upload_id, $upload_parts, $options);
            $this->assertEquals("200", $result['_info']['http_code']);
            $this->assertEquals("{\"Status\":\"OK\"}", $result['body']);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }

    public function testMultipartUploadCallbackFailed()
    {
        $object = "multipart-callback-test.txt";
        $copiedObject = "multipart-callback-test.txt.copied";
        $this->ossClient->putObject($this->bucket, $copiedObject, file_get_contents(__FILE__));

        /**
         *  step 1. 初始化一个分块上传事件, 也就是初始化上传Multipart, 获取upload id
         */
        try {
            $upload_id = $this->ossClient->initiateMultipartUpload($this->bucket, $object);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
        /*
         * step 2. uploadPartCopy
         */
        $copyId = 1;
        $eTag = $this->ossClient->uploadPartCopy($this->bucket, $copiedObject, $this->bucket, $object, $copyId, $upload_id);
        $upload_parts[] = array(
            'PartNumber' => $copyId,
            'ETag' => $eTag,
        );

        try {
            $listPartsInfo = $this->ossClient->listParts($this->bucket, $object, $upload_id);
            $this->assertNotNull($listPartsInfo);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        /**
         * step 3.
         */
        
        $json = 
        '{
            "callbackUrl":"www.baidu.com",
            "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
            "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
            "callbackBodyType":"application/json"
        }';
            
       $var = 
       '{
       "x:var1":"value1",
       "x:var2":"值2"
       }';
       $options = array(OssClient::OSS_CALLBACK => $json,
                        OssClient::OSS_CALLBACK_VAR => $var
                       );
        
        try {
            $result = $this->ossClient->completeMultipartUpload($this->bucket, $object, $upload_id, $upload_parts, $options);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

    }
   
    public function testPutObjectCallbackNormal()
    {
        //json
        {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
                "callbackBodyType":"application/json"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackOk($options, "200");
       }
       //url
       {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
                "callbackBodyType":"application/x-www-form-urlencoded"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackOk($options, "200");
        } 
        // Unspecified typre 
       {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackOk($options, "200");
        }
        //json and body is chinese
        {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"春水碧于天，画船听雨眠。垆边人似月，皓腕凝霜雪",
                "callbackBodyType":"application/json"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackOk($options, "200");
        }
        //url and body is chinese
        {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"春水碧于天，画船听雨眠。垆边人似月，皓腕凝霜雪",
                "callbackBodyType":"application/x-www-form-urlencoded"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackOk($options, "200");
        }
        //json and add callback_var
        {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
                "callbackBodyType":"application/json"
            }';
            
            $var = 
            '{
                "x:var1":"value1",
                "x:var2":"aliyun.com"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json,
                             OssClient::OSS_CALLBACK_VAR => $var
                             );
            $this->putObjectCallbackOk($options, "200");
        }
        //url and add callback_var
        {
            $json = 
            '{
                "callbackUrl":"callback.oss-demo.com:23450",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
                "callbackBodyType":"application/x-www-form-urlencoded"
            }';
            $var = 
            '{
                "x:var1":"value1凌波不过横塘路，但目送，芳",
                "x:var2":"值2"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json,
                             OssClient::OSS_CALLBACK_VAR => $var
                            );
            $this->putObjectCallbackOk($options, "200");
        }

    }

    public function testPutCallbackWithCallbackFailed()
    { 
        {
            $json = 
            '{
                "callbackUrl":"http://www.baidu.com",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
                "callbackBodyType":"application/json"
            }';
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackFailed($options, "");
        }

        {
            $json = 
            '{
                "callbackUrl":"http://www.baidu.com",
                "callbackHost":"oss-cn-hangzhou.aliyuncs.com",
                "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size}}",
                "callbackBodyType":"application/x-www-form-urlencoded"
            }';      
            $options = array(OssClient::OSS_CALLBACK => $json);
            $this->putObjectCallbackFailed($options, "");
        }

    }

    private function putObjectCallbackOk($options, $status)
    {
        $object = "oss-php-sdk-callback-test.txt";
        $content = file_get_contents(__FILE__);
        try {
            $result = $this->ossClient->putObject($this->bucket, $object, $content, $options);
            $this->assertEquals($status, $result['_info']['http_code']);
            $this->assertEquals("{\"Status\":\"OK\"}", $result['body']);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    private function putObjectCallbackFailed($options, $status)
    {
        $object = "oss-php-sdk-callback-test.txt";
        $content = file_get_contents(__FILE__);
        try {
            $result = $this->ossClient->putObject($this->bucket, $object, $content, $options);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }

    public function setUp()
    {
        parent::setUp();
    }
}
