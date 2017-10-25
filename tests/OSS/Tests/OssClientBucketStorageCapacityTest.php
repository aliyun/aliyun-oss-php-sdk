<?php
namespace OSS\Tests;

use OSS\Core\OssException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';

class OssClientBucketStorageCapacityTest extends TestOssClientBase
{
    public function testBucket()
    {
        try {
            $storageCapacity = $this->ossClient->getBucketStorageCapacity($this->bucket);
            $this->assertEquals($storageCapacity, - 1);
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
            $this->ossClient->putBucketStorageCapacity($this->bucket, - 2);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertEquals('InvalidArgument', $e->getErrorCode());
        }
    }
}
