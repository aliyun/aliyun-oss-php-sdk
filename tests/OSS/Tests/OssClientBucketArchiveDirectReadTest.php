<?php

namespace OSS\Tests;

use OSS\Core\OssException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketArchiveDirectReadTest extends TestOssClientBase
{
    public function testBucketArchiveDirectRead()
    {

        try {
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketArchiveDirectRead($this->bucket);
            $this->assertEquals(false,$status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketArchiveDirectRead($this->bucket, true);
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketArchiveDirectRead($this->bucket);
            $this->assertEquals(true, $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketArchiveDirectRead($this->bucket, false);
            Common::waitMetaSync();
            $status = $this->ossClient->getBucketArchiveDirectRead($this->bucket);
            $this->assertEquals(false, $status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }
}
