<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\LifecycleAbortMultipartUpload;
use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleExpiration;
use OSS\Model\LifecycleFilter;
use OSS\Model\LifecycleNoncurrentVersionTransition;
use OSS\Model\LifecycleNot;
use OSS\Model\LifecycleRule;
use OSS\Model\LifecycleAction;
use OSS\Model\LifecycleTag;
use OSS\Model\LifecycleTransition;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketLifecycleTest extends TestOssClientBase
{

    public function testBucketLifecycleOld()
    {
        $lifecycleConfig = new LifecycleConfig();
        $actions = array();
        $actions[] = new LifecycleAction("Expiration", "Days", 3);
        $lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
        $lifecycleConfig->addRule($lifecycleRule);
        $actions = array();
        $actions[] = new LifecycleAction("Expiration", "Date", '2022-10-12T00:00:00.000Z');
        $lifecycleRule = new LifecycleRule("delete temporary files", "temporary/", "Enabled", $actions);
        $lifecycleConfig->addRule($lifecycleRule);

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig2 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketLifecycle($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig3 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertNotEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }

    public function testSimpleLifeRule()
    {
        $lifecycleConfig = new LifecycleConfig();
        $lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled");
        $expiration = new LifecycleExpiration(3);
        $lifecycleRule->setExpiration($expiration);
        $lifecycleConfig->addRule($lifecycleRule);

        $lifecycleRule = new LifecycleRule("delete temporary files", "temporary/", "Enabled");
        $expiration = new LifecycleExpiration(null,"2022-10-12T00:00:00.000Z");
        $lifecycleRule->setExpiration($expiration);
        $lifecycleConfig->addRule($lifecycleRule);

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig2 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketLifecycle($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig3 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertNotEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }


    public function testManyLifeRule()
    {
        $lifecycleConfig = new LifecycleConfig();

        $rule1 = new LifecycleRule("rule1", "logs/", LifecycleRule::STATUS_ENANLED);
        $lifecycleExpiration = new LifecycleExpiration();
        $lifecycleExpiration->setDays(3);
        $rule1->setExpiration($lifecycleExpiration);

        $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
        $lifecycleAbortMultipartUpload->setDays(1);
        $rule1->setAbortMultipartUpload($lifecycleAbortMultipartUpload);

        $lifecycleConfig->addRule($rule1);

        $rule2 = new LifecycleRule("rule2", "logs2/", LifecycleRule::STATUS_ENANLED);
        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $rule2->addTransition($lifecycleTransition);
        // 60 天 转换Object的存储类型为 Archive
        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(60);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_ARCHIVE);
        $rule2->addTransition($lifecycleTransition);

        $lifecycleExpiration = new LifecycleExpiration();
        $lifecycleExpiration->setDays(180);
        $rule2->setExpiration($lifecycleExpiration);
        $lifecycleConfig->addRule($rule2);

        $rule3 = new LifecycleRule("rule3", "logs3/", LifecycleRule::STATUS_ENANLED);
        $lifecycleExpiration = new LifecycleExpiration();
        $lifecycleExpiration->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
        $rule3->setExpiration($lifecycleExpiration);

        $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
        $lifecycleAbortMultipartUpload->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
        $rule3->setAbortMultipartUpload($lifecycleAbortMultipartUpload);
        $lifecycleConfig->addRule($rule3);

        $rule4 = new LifecycleRule("rule4", "logs4/", LifecycleRule::STATUS_ENANLED);

        $tag = new LifecycleTag();
        $tag->setKey("key1");
        $tag->setValue("val1");
        $rule4->addTag($tag);

        $tag2 = new LifecycleTag();
        $tag2->setKey("key12");
        $tag2->setValue("val12");
        $rule4->addTag($tag2);
        $lifecycleConfig->addRule($rule4);
        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $rule4->addTransition($lifecycleTransition);

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            print_r($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig2 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketLifecycle($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig3 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertNotEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }

    public function testLifeRuleWithAccessTime()
    {
        $lifecycleConfig = new LifecycleConfig();

        $rule6 = new LifecycleRule("rule6", "logs6/", LifecycleRule::STATUS_ENANLED);

        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $lifecycleTransition->setIsAccessTime(true);
        $lifecycleTransition->setReturnToStdWhenVisit(false);
        $rule6->addTransition($lifecycleTransition);
        $lifecycleConfig->addRule($rule6);

        $rule7 = new LifecycleRule("rule7", "logs7/", LifecycleRule::STATUS_ENANLED);

        $nonTransition = new LifecycleNoncurrentVersionTransition();
        $nonTransition->setNoncurrentDays(30);
        $nonTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $nonTransition->setIsAccessTime(true);
        $nonTransition->setReturnToStdWhenVisit(true);
        $rule7->addNoncurrentVersionTransition($nonTransition);
        $lifecycleConfig->addRule($rule7);

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            print_r($e->getMessage());
            $this->assertTrue(true);
        }

        try {
            $this->ossClient->putBucketAccessMonitor($this->bucket, "Enabled");
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            print_r($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig2 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketLifecycle($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig3 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertNotEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }

    public function testLifeRuleWithFilter()
    {
        $lifecycleConfig = new LifecycleConfig();

        $rule = new LifecycleRule("rule-filter", "logs", LifecycleRule::STATUS_ENANLED);

        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $expiration = new LifecycleExpiration(100,null);

        $rule->addTransition($lifecycleTransition);
        $rule->setExpiration($expiration);

        $not = new LifecycleNot();
        $tag = new LifecycleTag();
        $tag->setKey("key1");
        $tag->setValue("val1");
        $not->setTag($tag);
        $not->setPrefix("logs1/");

        $filter = new LifecycleFilter();

        $filter->addNot($not);

        $rule->setFilter($filter);
        $lifecycleConfig->addRule($rule);

        try {
            $this->ossClient->putBucketLifecycle($this->bucket, $lifecycleConfig);
        } catch (OssException $e) {
            print_r($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig2 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketLifecycle($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $lifecycleConfig3 = $this->ossClient->getBucketLifecycle($this->bucket);
            $this->assertNotEquals($lifecycleConfig->serializeToXml(), $lifecycleConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }
}
