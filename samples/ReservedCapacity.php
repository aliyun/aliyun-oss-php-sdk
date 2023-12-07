<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\CreateReservedCapacity;
use OSS\Model\UpdateReservedCapacity;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// Create Reserved Capacity
$name = "test-tc-demo";
$dataRedundancyType = "LRS";
$reservedCapacity = 10240;
$config = new CreateReservedCapacity($name,$dataRedundancyType,$reservedCapacity);
$result = $ossClient->createReservedCapacity($config);
Common::println("Reserved Capacity Id:".$result['x-oss-reserved-capacity-id']);
Common::println("Create Reserved Capacity Success");

// Update Reserved Capacity
$status = "Enabled";
$reservedCapacity = 10240;
$autoExpansionSize = 100;
$autoExpansionMaxSize = 20480;
$id = "61f545e1-10f5-4c65-********";
$update = new UpdateReservedCapacity($status,$reservedCapacity,$autoExpansionSize,$autoExpansionMaxSize);
$result = $ossClient->updateReservedCapacity($id,$update);
Common::println("Reserved Capacity Id:".$result['x-oss-reserved-capacity-id']);
Common::println("Update Reserved Capacity Success");

// List Buckets Under The Reserved Capacity
$id = "a1b398e6-c4e5-481b-8638-********";
$result = $ossClient->listBucketWithReservedCapacity($id);
Common::println("Reserved Capacity Id:" . $result->getInstanceId());
if ($result->getBucketList() !== null) {
    foreach ($result->getBucketList() as $bucket) {
        Common::println("Reserved Capacity Bucket Name:" . $bucket);
    }

}


// Get Reserved Capacity By Id
$id = "Reserved-Capacity-Id";
$record = $ossClient->getReservedCapacity($id);
Common::println("Reserved Capacity Owner Id:".$record->getOwnerId());
Common::println("Reserved Capacity Owner Display Name:".$record->getOwnerDisplayName());
Common::println("Reserved Capacity Instance Id:".$record->getInstanceId());
Common::println("Reserved Capacity Name:".$record->getName());
Common::println("Reserved Capacity Region:".$record->getRegion());
Common::println("Reserved Capacity Status:".$record->getStatus());
Common::println("Reserved Capacity Data Redundancy Type:".$record->getDataRedundancyType());
Common::println("Reserved Capacity:".$record->getReservedCapacity());
if ($record->getAutoExpansionSize() != null){
    Common::println("Reserved Capacity Auto Expansion Size:".$record->getAutoExpansionSize());
}
if ($record->getAutoExpansionMaxSize() !== null){
    Common::println("Reserved Capacity Auto Expansion Max Size:".$record->getAutoExpansionMaxSize());
}
Common::println("Reserved Capacity Create Time:".$record->getCreateTime());
if ($record->getLastModifyTime() !== null){
    Common::println("Reserved Capacity Last Modify Time:".$record->getLastModifyTime());
}
if ($record->getEnableTime() !== null){
    Common::println("Reserved Capacity Enable Time:".$record->getEnableTime());
}

// List Reserved Capacity
$result = $ossClient->listReservedCapacity();
if ($result->getReservedCapacityList() !== null) {
    foreach ($result->getReservedCapacityList() as $record){
        Common::println("Reserved Capacity Owner Id:".$record->getOwnerId());
        Common::println("Reserved Capacity Owner Display Name:".$record->getOwnerDisplayName());
        Common::println("Reserved Capacity Instance Id:".$record->getInstanceId());
        Common::println("Reserved Capacity Name:".$record->getName());
        Common::println("Reserved Capacity Region:".$record->getRegion());
        Common::println("Reserved Capacity Status:".$record->getStatus());
        Common::println("Reserved Capacity Data Redundancy Type:".$record->getDataRedundancyType());
        Common::println("Reserved Capacity:".$record->getReservedCapacity());
        if ($record->getAutoExpansionSize() != null){
            Common::println("Reserved Capacity Auto Expansion Size:".$record->getAutoExpansionSize());
        }
        if ($record->getAutoExpansionMaxSize() !== null){
            Common::println("Reserved Capacity Auto Expansion Max Size:".$record->getAutoExpansionMaxSize());
        }
        Common::println("Reserved Capacity Create Time:".$record->getCreateTime());
        if ($record->getLastModifyTime() !== null){
            Common::println("Reserved Capacity Last Modify Time:".$record->getLastModifyTime());
        }
        if ($record->getEnableTime() !== null){
            Common::println("Reserved Capacity Enable Time:".$record->getEnableTime());
        }
    }
}
//******************************* For complete usage, see the following functions ****************************************************

createReservedCapacity($ossClient);
updateReservedCapacity($ossClient);
listBucketWithReservedCapacity($ossClient);
getReservedCapacity($ossClient);
listReservedCapacity($ossClient);

/**
 * Create Reserved Capacity
 *
 * @param OssClient $ossClient OssClient instance
 * @return null
 * @throws RequestCore_Exception
 */
function createReservedCapacity($ossClient)
{
    try {
        $name = "test-tc-demo";
        $dataRedundancyType = "LRS";
        $reservedCapacity = 10240;
        $config = new CreateReservedCapacity($name,$dataRedundancyType,$reservedCapacity);
        $result = $ossClient->createReservedCapacity($config);
        Common::println("Reserved Capacity Id:".$result['x-oss-reserved-capacity-id']);
        Common::println("Create Reserved Capacity Success");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Update Reserved Capacity By Id
 *
 * @param OssClient $ossClient OssClient instance
 * @return null
 * @throws RequestCore_Exception
 */
function updateReservedCapacity($ossClient)
{
    try {
        $status = "Enabled";
        $reservedCapacity = 10240;
        $autoExpansionSize = 100;
        $autoExpansionMaxSize = 20480;
        $id = "Reserved-Capacity-Id";
        $update = new UpdateReservedCapacity($status,$reservedCapacity,$autoExpansionSize,$autoExpansionMaxSize);
        $result = $ossClient->updateReservedCapacity($id,$update);
        Common::println("Reserved Capacity Id:".$result['x-oss-reserved-capacity-id']);
        Common::println("Update Reserved Capacity Success");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Update Reserved Capacity By Id
 *
 * @param OssClient $ossClient OssClient instance
 * @return null
 * @throws RequestCore_Exception
 */
function listBucketWithReservedCapacity($ossClient)
{
    try {
        $id = "a1b398e6-c4e5-481b-8638-********";
        $result = $ossClient->listBucketWithReservedCapacity($id);
        Common::println("Reserved Capacity Id:" . $result->getInstanceId());
        if ($result->getBucketList() !== null) {
            foreach ($result->getBucketList() as $bucket) {
                Common::println("Reserved Capacity Bucket Name:" . $bucket);
            }

        }
        Common::println("List Buckets With Reserved Capacity Success");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Reserved Capacity By Id
 *
 * @param OssClient $ossClient OssClient instance
 * @return null
 * @throws RequestCore_Exception
 */
function getReservedCapacity($ossClient)
{
    try {
        $id = "Reserved-Capacity-Id";
        $record = $ossClient->getReservedCapacity($id);
        Common::println("Reserved Capacity Owner Id:".$record->getOwnerId());
        Common::println("Reserved Capacity Owner Display Name:".$record->getOwnerDisplayName());
        Common::println("Reserved Capacity Instance Id:".$record->getInstanceId());
        Common::println("Reserved Capacity Name:".$record->getName());
        Common::println("Reserved Capacity Region:".$record->getRegion());
        Common::println("Reserved Capacity Status:".$record->getStatus());
        Common::println("Reserved Capacity Data Redundancy Type:".$record->getDataRedundancyType());
        Common::println("Reserved Capacity:".$record->getReservedCapacity());
        if ($record->getAutoExpansionSize() != null){
            Common::println("Reserved Capacity Auto Expansion Size:".$record->getAutoExpansionSize());
        }
        if ($record->getAutoExpansionMaxSize() !== null){
            Common::println("Reserved Capacity Auto Expansion Max Size:".$record->getAutoExpansionMaxSize());
        }
        Common::println("Reserved Capacity Create Time:".$record->getCreateTime());
        if ($record->getLastModifyTime() !== null){
            Common::println("Reserved Capacity Last Modify Time:".$record->getLastModifyTime());
        }
        if ($record->getEnableTime() !== null){
            Common::println("Reserved Capacity Enable Time:".$record->getEnableTime());
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * List Reserved Capacity By Id
 *
 * @param OssClient $ossClient OssClient instance
 * @return null
 * @throws RequestCore_Exception
 */
function listReservedCapacity($ossClient)
{
    try {
        $result = $ossClient->listReservedCapacity();
        if ($result->getReservedCapacityList() !== null) {
            foreach ($result->getReservedCapacityList() as $record) {
                Common::println("Reserved Capacity Owner Id:".$record->getOwnerId());
                Common::println("Reserved Capacity Owner Display Name:".$record->getOwnerDisplayName());
                Common::println("Reserved Capacity Instance Id:".$record->getInstanceId());
                Common::println("Reserved Capacity Name:".$record->getName());
                Common::println("Reserved Capacity Region:".$record->getRegion());
                Common::println("Reserved Capacity Status:".$record->getStatus());
                Common::println("Reserved Capacity Data Redundancy Type:".$record->getDataRedundancyType());
                Common::println("Reserved Capacity:".$record->getReservedCapacity());
                if ($record->getAutoExpansionSize() != null){
                    Common::println("Reserved Capacity Auto Expansion Size:".$record->getAutoExpansionSize());
                }
                if ($record->getAutoExpansionMaxSize() !== null){
                    Common::println("Reserved Capacity Auto Expansion Max Size:".$record->getAutoExpansionMaxSize());
                }
                Common::println("Reserved Capacity Create Time:".$record->getCreateTime());
                if ($record->getLastModifyTime() !== null){
                    Common::println("Reserved Capacity Last Modify Time:".$record->getLastModifyTime());
                }
                if ($record->getEnableTime() !== null){
                    Common::println("Reserved Capacity Enable Time:".$record->getEnableTime());
                }
            }

        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}