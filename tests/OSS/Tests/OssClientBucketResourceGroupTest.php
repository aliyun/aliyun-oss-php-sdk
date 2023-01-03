<?php

namespace OSS\Tests;

use OSS\Core\OssException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketResourceGroupTest extends TestOssClientBase
{
    public function testBucketResourceGroup()
    {
        try {
            Common::waitMetaSync();
            $id = $this->ossClient->getBucketResourceGroup($this->bucket);
            $this->assertEquals("rg-acfmy7mo47b3adq", $id);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketResourceGroup($this->bucket, "rg-aekztgrh2colcoa");
            Common::waitMetaSync();
            $id = $this->ossClient->getBucketResourceGroup($this->bucket);
            $this->assertEquals("rg-aekztgrh2colcoa", $id);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }
}