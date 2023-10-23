<?php

namespace OSS\Tests;

use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Common.php';

class TestOssClientBase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OssClient
     */
    protected $ossClient;

    /**
     * @var string
     */
    protected $bucket;

    protected function setUp(): void
    {
        $this->bucket = Common::getBucketName() .'-'. time();
        $this->ossClient = Common::getOssClient();
        $options[OssClient::OSS_HEADERS]['prefix'] = Common::getBucketName() .'-';
        $list = $this->ossClient->listBuckets($options);
        foreach ($list->getBucketList() as $bucketInfo){
            $objects = $this->ossClient->listObjects($bucketInfo->getName(), array('max-keys' => 1000, 'delimiter' => ''))->getObjectList();
            $keys = array();
            foreach ($objects as $obj) {
                $keys[] = $obj->getKey();
            }
            if (count($keys) > 0) {
                $this->ossClient->deleteObjects($bucketInfo->getName(), $keys);
            }
            $uploads = $this->ossClient->listMultipartUploads($bucketInfo->getName())->getUploads();
            foreach ($uploads as $up) {
                $this->ossClient->abortMultipartUpload($bucketInfo->getName(), $up->getKey(), $up->getUploadId());
            }
            $this->ossClient->deleteBucket($bucketInfo->getName());
        }
        $this->ossClient->createBucket($this->bucket);

        Common::waitMetaSync();
    }

    protected function tearDown(): void
    {
        if (!$this->ossClient->doesBucketExist($this->bucket)) {
            return;
        }

        $objects = $this->ossClient->listObjects(
            $this->bucket, array('max-keys' => 1000, 'delimiter' => ''))->getObjectList();
        $keys = array();
        foreach ($objects as $obj) {
            $keys[] = $obj->getKey();
        }
        if (count($keys) > 0) {
            $this->ossClient->deleteObjects($this->bucket, $keys);
        }
        $uploads = $this->ossClient->listMultipartUploads($this->bucket)->getUploads();
        foreach ($uploads as $up) {
            $this->ossClient->abortMultipartUpload($this->bucket, $up->getKey(), $up->getUploadId());
        }

        $this->ossClient->deleteBucket($this->bucket);
    }
}
