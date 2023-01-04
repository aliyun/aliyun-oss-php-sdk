<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\StyleConfig;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketStyleTest extends TestOssClientBase
{
    public function testBucketStyle()
    {
        try {
            $config = new StyleConfig();
            $config->setName("image-style");
            $config->setContent("image/resize,w_100");
            $this->ossClient->putBucketStyle($this->bucket, $config);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        Common::waitMetaSync();

        try {
            $result = $this->ossClient->getBucketStyle($this->bucket, "image-style");
            $this->assertEquals("image-style",$result->getName());
            $this->assertEquals("image/resize,w_100",$result->getContent());
            $this->assertNotEmpty($result->getCreateTime());
            $this->assertNotEmpty($result->getLastModifyTime());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        Common::waitMetaSync();
        try {
            $config = new StyleConfig();
            $config->setName("image-style1");
            $config->setContent("image/resize,w_1200");
            $this->ossClient->putBucketStyle($this->bucket, $config);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $result = $this->ossClient->listBucketStyle($this->bucket);
            $styleList = $result->getStyleList();
            $this->assertEquals("image-style",$styleList[0]->getName());
            $this->assertEquals("image/resize,w_100",$styleList[0]->getContent());
            $this->assertNotEmpty($styleList[0]->getCreateTime());
            $this->assertNotEmpty($styleList[0]->getLastModifyTime());

            $this->assertEquals("image-style1",$styleList[1]->getName());
            $this->assertEquals("image/resize,w_1200",$styleList[1]->getContent());
            $this->assertNotEmpty($styleList[1]->getCreateTime());
            $this->assertNotEmpty($styleList[1]->getLastModifyTime());

        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            $this->ossClient->deleteBucketStyle($this->bucket,'image-style');

        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->deleteBucketStyle($this->bucket,'image-style1');
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            $result = $this->ossClient->listBucketStyle($this->bucket);
            $styleList = $result->getStyleList();
            $this->assertEquals(0,count($styleList));
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }
}