<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleNoncurrentVersionTransition;
use OSS\Model\LifecycleRule;
use OSS\Model\LifecycleTransition;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketAccessMonitorTest extends TestOssClientBase
{
    public function testBucketAccessMonitor()
    {
        try {
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketAccessMonitor($this->bucket);
            $this->assertEquals("Disabled", $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketAccessMonitor($this->bucket, "Enabled");
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketAccessMonitor($this->bucket);
            $this->assertEquals("Enabled", $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketAccessMonitor($this->bucket, "Disabled");
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketAccessMonitor($this->bucket);
            $this->assertEquals("Disabled", $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }


    public function testBucketAccessMonitorWithLifeRule(){
        try {
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketAccessMonitor($this->bucket);
            $this->assertEquals("Disabled", $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketAccessMonitor($this->bucket, "Enabled");
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketAccessMonitor($this->bucket);
            $this->assertEquals("Enabled", $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        $lifecycleConfig = new LifecycleConfig();

        $rule7 = new LifecycleRule("rule7", "logs7/", LifecycleRule::STATUS_ENANLED);

        $nonTransition = new LifecycleNoncurrentVersionTransition();
        $nonTransition->setNoncurrentDays(30);
        $nonTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $nonTransition->setIsAccessTime(true);
        $nonTransition->setReturnToStdWhenVisit(true);

        $rule7->addNonCurrentVersionTransition($nonTransition);
        $lifecycleConfig->addRule($rule7);

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            print_r($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketAccessMonitor($this->bucket, "Disabled");
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->ossClient->deleteBucketLifecycle($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketAccessMonitor($this->bucket, "Disabled");
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketAccessMonitor($this->bucket);
            $this->assertEquals("Disabled", $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }
}
