<?php
namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\OssClient;

class OssClientBucketStorageCapacityTest extends \PHPUnit_Framework_TestCase
{
    public function testBucket()
    {
        try {
            $storageCapacity = $this->ossClient->getBucketStorageCapacity($this->bucket);
            $this->assertEquals($storageCapacity, -1);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketStorageCapacity($this->bucket, 1000);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $storageCapacity = $this->ossClient->getBucketStorageCapacity($this->bucket);
            $this->assertEquals($storageCapacity, 1000);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketStorageCapacity($this->bucket, 0);

            Common::waitMetaSync();

            $storageCapacity = $this->ossClient->getBucketStorageCapacity($this->bucket);
            $this->assertEquals($storageCapacity, 0);

            $this->ossClient->putObject($this->bucket, 'test-storage-capacity','test-content');
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertEquals('Bucket storage exceed max storage capacity.',$e->getErrorMessage());
        }

        try {
            $this->ossClient->putBucketStorageCapacity($this->bucket, - 2);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertEquals(400, $e->getHTTPStatus());
            $this->assertEquals('InvalidArgument', $e->getErrorCode());
        }
    }

    public function tearDown()
    {
        $this->ossClient->deleteObject($this->bucket, 'test-storage-capacity');
        $this->ossClient->putBucketStorageCapacity($this->bucket,-1);
    }
}
