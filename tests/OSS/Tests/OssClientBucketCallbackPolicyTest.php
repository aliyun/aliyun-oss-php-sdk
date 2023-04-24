<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\CallbackPolicyConfig;
use OSS\Model\CallbackPolicyItem;
require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketCallbackPolicyTest extends TestOssClientBase
{

    public function testBucketCallbackPolicy()
    {
        $config = new CallbackPolicyConfig();
        $name = "first";
        $callback = base64_encode('{"callbackUrl":"http://www.aliyuncs.com", "callbackBody":"bucket=${bucket}&object=${object}"}');

        $policyItem = new CallbackPolicyItem($name,$callback);
        $config->addPolicyItem($policyItem);

        try {
            $this->ossClient->putBucketCallbackPolicy($this->bucket, $config);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $config2 = $this->ossClient->getBucketCallbackPolicy($this->bucket);
            $this->assertEquals($config->serializeToXml(), $config2->serializeToXml());
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketCallbackPolicy($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $this->ossClient->getBucketCallbackPolicy($this->bucket);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }
}
