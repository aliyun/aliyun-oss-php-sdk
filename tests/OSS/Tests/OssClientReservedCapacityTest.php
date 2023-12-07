<?php
namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\CreateReservedCapacity;
use OSS\Model\UpdateReservedCapacity;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';

class OssClientReservedCapacityTest extends TestOssClientBase
{
    public function testReservedCapacity()
    {

        try {
            $name = "php-sdk-test-".time();
            $dataRedundancyType = "LRS";
            $reservedCapacity = 10240;
            $create = new CreateReservedCapacity($name,$dataRedundancyType,$reservedCapacity);
            $createRs = $this->ossClient->createReservedCapacity($create);
            $this->assertTrue(true);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        $id = $createRs['x-oss-reserved-capacity-id'];

        try {
            $status = "Enabled";
            $reservedCapacity = 10240;
            $autoExpansionSize = 100;
            $autoExpansionMaxSize = 20480;
            $update = new UpdateReservedCapacity($status,$reservedCapacity,$autoExpansionSize,$autoExpansionMaxSize);
            $updateRs = $this->ossClient->updateReservedCapacity($id, $update);
            $this->assertEquals($updateRs['x-oss-reserved-capacity-id'], $id);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $record = $this->ossClient->getReservedCapacity($id);
            $this->assertEquals($record->getInstanceId(), $id);
            $this->assertEquals($record->getStatus(), $status);
            $this->assertEquals($record->getName(), $name);
            $this->assertNotEmpty($record->getOwnerDisplayName());
            $this->assertNotEmpty($record->getOwnerId());
            $this->assertNotEmpty($record->getRegion());
            $this->assertEquals($record->getDataRedundancyType(),$dataRedundancyType);
            $this->assertEquals($record->getReservedCapacity(),$reservedCapacity);
            $this->assertNotEmpty($record->getCreateTime());
            $this->assertNotEmpty($record->getLastModifyTime());
            $this->assertNotEmpty($record->getEnableTime());
            $this->assertEquals($record->getAutoExpansionSize(), $autoExpansionSize);
            $this->assertEquals($record->getAutoExpansionMaxSize(), $autoExpansionMaxSize);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $bucketList = $this->ossClient->listBucketWithReservedCapacity($id);
            $this->assertEquals($bucketList->getInstanceId(), $id);
            $this->assertNull($bucketList->getBucketList());
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $list = $this->ossClient->listReservedCapacity();
            $this->assertTrue(count($list->getReservedCapacityList()) > 0);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $options = array(
                OssClient::OSS_STORAGE => OssClient::OSS_STORAGE_RESERVEDCAPACITY,
                OssClient::OSS_REDUNDANCY => OssClient::OSS_REDUNDANCY_LRS,
                OssClient::OSS_RESERVED_CAPACITY_ID => $id

            );
            $bucket = 'php-sdk-bucket-'. time();
            $this->ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PRIVATE,$options);
            $this->assertTrue(true);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $bucketList = $this->ossClient->listBucketWithReservedCapacity($id);
            $this->assertEquals($bucketList->getInstanceId(), $id);
            $this->assertTrue(count($bucketList->getBucketList()) == 1);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $bucketInfo = $this->ossClient->getBucketInfo($bucket);
            $this->assertEquals($bucketInfo->getReservedCapacityInstanceId(), $id);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }


        try {
            $bucketStat = $this->ossClient->getBucketStat($bucket);
            $this->assertEquals($bucketStat->getReservedCapacityStorage(), 0);
            $this->assertEquals($bucketStat->getReservedCapacityObjectCount(), 0);
            $this->assertEquals($bucketStat->getDeepColdArchiveStorage(), 0);
            $this->assertEquals($bucketStat->getDeepColdArchiveRealStorage(), 0);
            $this->assertEquals($bucketStat->getDeepColdArchiveObjectCount(), 0);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }



        $this->ossClient->deleteBucket($bucket);


    }
}
