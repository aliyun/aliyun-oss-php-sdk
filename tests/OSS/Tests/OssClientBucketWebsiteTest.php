<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\WebsiteCondition;
use OSS\Model\WebsiteConfig;
use OSS\Model\WebsiteErrorDocument;
use OSS\Model\WebsiteIncludeHeader;
use OSS\Model\WebsiteIndexDocument;
use OSS\Model\WebsiteMirrorHeaders;
use OSS\Model\WebsiteMirrorHeadersSet;
use OSS\Model\WebsiteRedirect;
use OSS\Model\WebsiteRoutingRule;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketWebsiteTest extends TestOssClientBase
{
    public function testBucketSample()
    {

        $index = new WebsiteIndexDocument("index.html");
        $error = new WebsiteErrorDocument("error.html");
        $websiteConfig = new WebsiteConfig($index, $error);
        try {
            $this->ossClient->putBucketWebsite($this->bucket, $websiteConfig);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $websiteConfig2 = $this->ossClient->getBucketWebsite($this->bucket);
            $this->assertEquals("index.html", $websiteConfig2->getIndexDocument()->getSuffix());
            $this->assertEquals("error.html", $websiteConfig2->getErrorDocument()->getKey());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketWebsite($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $websiteConfig3 = $this->ossClient->getBucketLogging($this->bucket);
            $this->assertNotEquals($websiteConfig->serializeToXml(), $websiteConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }


    public function testBucketWithMirrorOne(){
        $index = new WebsiteIndexDocument("index.html");
        $error = new WebsiteErrorDocument("error.html");
        $index->setSupportSubDir(false);
        $index->setType(0);
        $error->setHttpStatus(404);
        $websiteConfig = new WebsiteConfig($index, $error);

        $routingRule = new WebsiteRoutingRule();
        $routingRule->setNumber(1);
        $websiteCondition = new WebsiteCondition();
        $websiteCondition->setKeyPrefixEquals("abc");
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);
        $routingRule->setCondition($websiteCondition);

        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::MIRROR);
        $websiteRedirect->setPassQueryString(true);
        $websiteRedirect->setMirrorURL('https://www.example.com/');
        $websiteRedirect->setMirrorPassQueryString(true);
        $websiteRedirect->setMirrorFollowRedirect(true);
        $websiteRedirect->setMirrorCheckMd5(true);

        $mirrorHeaders = new WebsiteMirrorHeaders();
        $mirrorHeaders->setPassAll(true);
        $pass = 'cache-control-one';
        $passOne = 'pass-one';
        $mirrorHeaders->addPass($pass);
        $mirrorHeaders->addPass($passOne);
        $remove = 'remove-one';
        $removeOne = 'test-two';
        $mirrorHeaders->addRemove($remove);
        $mirrorHeaders->addRemove($removeOne);

        $set = new WebsiteMirrorHeadersSet();
        $set->setKey("key1");
        $set->setValue("val1");

        $mirrorHeaders->addSet($set);

        $setOne = new WebsiteMirrorHeadersSet();
        $setOne->setKey("key2");
        $setOne->setValue("val2");

        $mirrorHeaders->addSet($setOne);
        $websiteRedirect->setMirrorHeaders($mirrorHeaders);
//        $websiteRedirect->setReplaceKeyPrefixWith('def/');
        $websiteRedirect->setEnableReplacePrefix(false);
        $routingRule->setRedirect($websiteRedirect);

        $websiteConfig->addRule($routingRule);

        try {
            $this->ossClient->putBucketWebsite($this->bucket, $websiteConfig);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $websiteConfig2 = $this->ossClient->getBucketWebsite($this->bucket);
            print_r($websiteConfig2->serializeToXml());
            print_r($websiteConfig->serializeToXml());
            $this->assertEquals($websiteConfig->serializeToXml(), $websiteConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketWebsite($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $websiteConfig3 = $this->ossClient->getBucketLogging($this->bucket);
            $this->assertNotEquals($websiteConfig->serializeToXml(), $websiteConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }


    public function testBucketWithMirrorTwo(){
        $index = new WebsiteIndexDocument("index.html");
        $error = new WebsiteErrorDocument("error.html");
        $index->setSupportSubDir(false);
        $index->setType(0);
        $error->setHttpStatus(404);
        $websiteConfig = new WebsiteConfig($index, $error);

        $routingRule = new WebsiteRoutingRule();

        $routingRule->setNumber(1);
        $websiteCondition = new WebsiteCondition();
        $websiteCondition->setKeyPrefixEquals("examplebucket");
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);
        $routingRule->setCondition($websiteCondition);

        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::MIRROR);
        $websiteRedirect->setPassQueryString(true);
        $websiteRedirect->setMirrorURL('https://www.example.com/');
        $websiteRedirect->setMirrorPassQueryString(true);
        $websiteRedirect->setMirrorFollowRedirect(true);
        $websiteRedirect->setMirrorCheckMd5(true);

        $mirrorHeaders = new WebsiteMirrorHeaders();
        $mirrorHeaders->setPassAll(true);

        $mirrorHeaders = new WebsiteMirrorHeaders();
        $mirrorHeaders->setPassAll(true);
        $pass = 'cache-control-one';
        $passOne = 'pass-one';
        $mirrorHeaders->addPass($pass);
        $mirrorHeaders->addPass($passOne);
        $remove = 'remove-one';
        $removeOne = 'test-two';
        $mirrorHeaders->addRemove($remove);
        $mirrorHeaders->addRemove($removeOne);

        $set = new WebsiteMirrorHeadersSet();
        $set->setKey("key1");
        $set->setValue("val1");

        $mirrorHeaders->addSet($set);
        $websiteRedirect->setMirrorHeaders($mirrorHeaders);
//        $websiteRedirect->setReplaceKeyPrefixWith('examplebucket');
        $websiteRedirect->setEnableReplacePrefix(true);
        $routingRule->setRedirect($websiteRedirect);
        $websiteConfig->addRule($routingRule);

        $routingRuleOne = new WebsiteRoutingRule();
        $routingRuleOne->setNumber(2);
        $websiteCondition = new WebsiteCondition();
        $includeHeader = new WebsiteIncludeHeader();
        $includeHeader->setKey('host');
        $includeHeader->setEquals('test.oss-cn-beijing-internal.aliyuncs.com');
        $websiteCondition->addIncludeHeader($includeHeader);
        $includeHeader->setKey('host_two');
        $includeHeader->setEquals('demo.oss-cn-beijing-internal.aliyuncs.com');
        $websiteCondition->addIncludeHeader($includeHeader);
        $websiteCondition->setKeyPrefixEquals('abc/');
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);
        $routingRuleOne->setCondition($websiteCondition);
        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::ALICDN);
        $websiteRedirect->setProtocol(WebsiteRedirect::HTTP);
        $websiteRedirect->setPassQueryString(false);
//        $websiteRedirect->setReplaceKeyWith('prefix/${key}.jpg');
        $websiteRedirect->setEnableReplacePrefix(false);
        $websiteRedirect->setHttpRedirectCode(301);
        $routingRuleOne->setRedirect($websiteRedirect);
        $websiteConfig->addRule($routingRuleOne);

        $routingRuleTwo = new WebsiteRoutingRule();
        $routingRuleTwo->setNumber(3);
        $websiteCondition = new WebsiteCondition();
        $websiteCondition->setKeyPrefixEquals("abc/");
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);
        $routingRuleTwo->setCondition($websiteCondition);

        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::EXTERNAL);
        $websiteRedirect->setProtocol(WebsiteRedirect::HTTPS);
        $websiteRedirect->setHostName("demo.com");
        $websiteRedirect->setPassQueryString(false);
//        $websiteRedirect->setReplaceKeyWith('prefix/${key}');
        $websiteRedirect->setEnableReplacePrefix(false);
        $websiteRedirect->setHttpRedirectCode(302);

        $routingRuleTwo->setRedirect($websiteRedirect);
        $websiteConfig->addRule($routingRuleTwo);


        try {
            $this->ossClient->putBucketWebsite($this->bucket, $websiteConfig);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $websiteConfig2 = $this->ossClient->getBucketWebsite($this->bucket);
            $this->assertEquals($websiteConfig->serializeToXml(), $websiteConfig2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketWebsite($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $websiteConfig3 = $this->ossClient->getBucketLogging($this->bucket);
            $this->assertNotEquals($websiteConfig->serializeToXml(), $websiteConfig3->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }
}
