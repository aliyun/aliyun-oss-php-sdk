<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\RefererConfig;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketRefererTest extends TestOssClientBase
{

    public function testBucket()
    {
        $refererConfig = new RefererConfig();
        $refererConfig->addReferer('http://www.aliyun.com');

        try {
            $this->ossClient->putBucketReferer($this->bucket, $refererConfig);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $refererConfig2 = $this->ossClient->getBucketReferer($this->bucket);
            $refererConfig->setAllowTruncateQueryString(true);
            $this->assertEquals($refererConfig->serializeToXml(), $refererConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $nullRefererConfig = new RefererConfig();
            $nullRefererConfig->setAllowEmptyReferer(false);
            $this->ossClient->putBucketReferer($this->bucket, $nullRefererConfig);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $refererConfig3 = $this->ossClient->getBucketLogging($this->bucket);
            $this->assertNotEquals($refererConfig->serializeToXml(), $refererConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }

    public function testBucketReferer()
    {

        try {
            $referer = $this->ossClient->getBucketReferer($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        $refererConfig = new RefererConfig();
        $refererConfig->addReferer('http://www.aliyun.com');

        try {
            $this->ossClient->putBucketReferer($this->bucket, $refererConfig);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $refererConfig2 = $this->ossClient->getBucketReferer($this->bucket);
            $refererConfig->setAllowTruncateQueryString(true);
            $this->assertEquals($refererConfig->serializeToXml(), $refererConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $nullRefererConfig = new RefererConfig();
            $nullRefererConfig->setAllowEmptyReferer(false);
            $nullRefererConfig->setAllowTruncateQueryString(false);
            $nullRefererConfig->addReferer('http://www.aliyun.com');

            $nullRefererConfig->addBlackReferer('http://www.refuse.com');
            $nullRefererConfig->addBlackReferer('https://www.refuse.com');
            $this->ossClient->putBucketReferer($this->bucket, $nullRefererConfig);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $refererConfig3 = $this->ossClient->getBucketReferer($this->bucket);
            $this->assertEquals($refererConfig3->serializeToXml(), $nullRefererConfig->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $refererConfig4 = new RefererConfig();
            $this->ossClient->putBucketReferer($this->bucket,$refererConfig4);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $refererConfig5 = $this->ossClient->getBucketReferer($this->bucket);
            $this->assertEquals($referer->serializeToXml(), $refererConfig5->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


    }
}
