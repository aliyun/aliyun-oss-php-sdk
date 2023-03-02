<?php
namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\ReplicationConfig;
use OSS\Model\ReplicationDestination;
use OSS\Model\ReplicationEncryptionConfiguration;
use OSS\Model\ReplicationRule;
use OSS\Model\ReplicationSourceSelectionCriteria;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';

class OssClientBucketReplicationTest extends TestOssClientBase
{

    private $descBucket;
    protected function setUp(): void
    {
        parent::setUp();
        $this->descBucket = $this->bucket . 'desc';
        $this->ossClient->createBucket($this->descBucket);
    }
    public function testBucketReplication(){


        $replicationConfig = new ReplicationConfig();

        $rule = new ReplicationRule();
        $rule->setPrefixSet('prefix_5');
        $rule->setPrefixSet('prefix_6');
        $rule->setAction(ReplicationRule::ACTION_PUT);
        $rule->setHistoricalObjectReplication('enabled');
        $rule->setSyncRole('aliyunramrole');
        $destination = new ReplicationDestination();
        $destination->setBucket($this->descBucket);
        $destination->setLocation('oss-cn-hangzhou');
        $destination->setTransferType('internal');
        $rule->addDestination($destination);
        $criteria = new ReplicationSourceSelectionCriteria();
        $criteria->setStatus("Enabled");
        $rule->addSourceSelectionCriteria($criteria);
        $encryptionConfiguration = new ReplicationEncryptionConfiguration();
        $encryptionConfiguration->setReplicaKmsKeyID("c4d49f85-ee30-426b-a5ed-95e9139d");
        $rule->addEncryptionConfiguration($encryptionConfiguration);
        $replicationConfig->addRule($rule);

        try {
            $this->ossClient->putBucketReplication($this->bucket, $replicationConfig);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $replicationConfig2 = $this->ossClient->getBucketReplication($this->bucket);
            $rules = $replicationConfig2->getRules();
            $rule = $rules[0];
            $id = $rule->getId();
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putBucketRtc($this->bucket, $id,'disabled');
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $result = $this->ossClient->getBucketReplicationLocation($this->bucket);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }


        try {
            $result = $this->ossClient->getBucketReplicationProgress($this->bucket,$id);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $result = $this->ossClient->deleteBucketReplication($this->bucket,$id);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }

    protected function tearDown(): void
    {
        if (!$this->ossClient->doesBucketExist($this->descBucket)) {
            return;
        }

        $objects = $this->ossClient->listObjects(
            $this->bucket . 'desc', array('max-keys' => 1000, 'delimiter' => ''))->getObjectList();
        $keys = array();
        foreach ($objects as $obj) {
            $keys[] = $obj->getKey();
        }
        if (count($keys) > 0) {
            $this->ossClient->deleteObjects($this->descBucket, $keys);
        }
        $uploads = $this->ossClient->listMultipartUploads($this->descBucket)->getUploads();
        foreach ($uploads as $up) {
            $this->ossClient->abortMultipartUpload($this->descBucket, $up->getKey(), $up->getUploadId());
        }

        $this->ossClient->deleteBucket($this->descBucket);
        parent::tearDown();
    }

}
