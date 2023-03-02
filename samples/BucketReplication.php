<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\ReplicationConfig;
use OSS\Model\ReplicationRule;
use OSS\Model\ReplicationDestination;
use OSS\Model\ReplicationSourceSelectionCriteria;
use OSS\Model\ReplicationEncryptionConfiguration;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage ***************************************************************

// Set Bucket Replication rule
$replicationConfig = new ReplicationConfig();

$rule = new ReplicationRule();
// Set the prefix of the object to be copied
$rule->setPrefixSet('prefix_5');
$rule->setPrefixSet('prefix_6');
// Specify the operations that can be copied to the target bucket (ALL or PUT)
$rule->setAction(ReplicationRule::ACTION_PUT);
// Specify whether to copy historical data
$rule->setHistoricalObjectReplication('enabled');
// Which role is authorized for OSS to use for data replication
$rule->setSyncRole('aliyunramrole');
$destination = new ReplicationDestination();
// Specify the target bucket to which the data will be copied。
$destination->setBucket('test-demo5');
// The region where the target bucket is located。
$destination->setLocation('oss-cn-beijing');
// Specify the data transmission link used when data is copied. internal (default) oss_acc: transmission acceleration link
$destination->setTransferType('internal');

$rule->addDestination($destination);

$criteria = new ReplicationSourceSelectionCriteria();

// Specify whether OSS copies objects created through SSE-KMS encryption
$criteria->setStatus("Enabled");
$rule->addSourceSelectionCriteria($criteria);
// Specify SSE-KMS key ID. If you specify Status as Enabled, you must specify this element
$encryption = new ReplicationEncryptionConfiguration();
$encryption->setReplicaKmsKeyID("c4d49f85-ee30-426b-a5ed-95e9139d");
$rule->addEncryptionConfiguration($encryption);

$replicationConfig->addRule($rule);
$ossClient->putBucketReplication($bucket,$replicationConfig);
Common::println("Bucket replication has created");

//The existing cross-region replication rules are turned on or off
$ossClient->putBucketRtc($bucket,"disabled","test_replication_rule_id");
printf("Bucket replication has closed");

// Get bucket Replication rule
$replicationResult = $ossClient->getBucketReplication($bucket);
Common::println("===Replication Rule  start ===");
foreach ($replicationResult->getRules() as $rule) {
    if ($rule->getId()){
        Common::println("Replication Id:". $rule->getId().PHP_EOL);
    }
    if ($rule->getPrefixSet()){
        foreach ($rule->getPrefixSet() as $prefix){
            Common::println("Replication Prefix: ".$prefix.PHP_EOL);
        }
    }
    if ($rule->getRTC()){
        Common::println("Replication RTC Status: ".$rule->getRTC().PHP_EOL);
    }
    if ($rule->getAction()){
        Common::println("Replication Action:". $rule->getAction().PHP_EOL);
    }
    if ($rule->getDestination()){
        Common::println("Replication Action:". $rule->getDestination()->getBucket().PHP_EOL);
        Common::println("Replication Target Bucket Location: ". $rule->getDestination()->getLocation().PHP_EOL);
        if($rule->getDestination()->getTransferType()) {
            Common::println("Replication Target Bucket TransferType: " . $rule->getDestination()->getTransferType() . PHP_EOL);
        }
    }
    if ($rule->getSourceSelectionCriteria()){
        Common::println("Replication Source Selection Criteria Status:". $rule->getSourceSelectionCriteria()->getStatus().PHP_EOL);
    }

    if ($rule->getEncryptionConfiguration()){
        Common::println("Replication Encryption Configuration Kms Key Id:". $rule->getEncryptionConfiguration()->getReplicaKmsKeyID().PHP_EOL);
    }
    Common::println("Replication HistoricalObjectReplication:". $rule->getHistoricalObjectReplication().PHP_EOL);
    Common::println("Replication SyncRole: ". $rule->getSyncRole().PHP_EOL);
    Common::println("Replication Status: ". $rule->getStatus().PHP_EOL);
}
Common::println("===Replication Rule End ===");

// Get Bucket Replication Location
$replicationResult = $ossClient->getBucketReplicationLocation($bucket);

if($replicationResult){
    Common::println("=====================Bucket replication location start=================================".PHP_EOL);
    if ($replicationResult->getLocations()){
        foreach ($replicationResult->getLocations() as $location){
            Common::println("Bucket replication location is ".$location.PHP_EOL);
        }
    }

    if ($replicationResult->getLocationTransferTypes()){
        foreach ($replicationResult->getLocationTransferTypes() as $type){
            Common::println("Bucket replication location LocationTransferType location is: ".$type['location'].PHP_EOL);
            Common::println("Bucket replication location LocationTransferType type is: ".$type['type'].PHP_EOL);
        }
    }

    Common::println("========================Bucket replication location end ============================".PHP_EOL);
}

// Get Bucket Replication Progress
$result = $ossClient->getBucketReplicationProgress($bucket,'test-replication-id');

$replicationProcessResult = $result->getRule();
if($replicationProcessResult){
    Common::println("=====================Bucket replication process start=================================".PHP_EOL);
    Common::println("Bucket replication process id is ".$replicationProcessResult->getId().PHP_EOL);
    if($replicationProcessResult->getPrefixSet()){
        $prefixSet = $replicationProcessResult->getPrefixSet();
        foreach ($prefixSet as $prefix){
            Common::println("Bucket replication process prefix is: ".$prefix.PHP_EOL);
        }
    }
    Common::println("Bucket replication process action is ".$replicationProcessResult->getAction().PHP_EOL);
    if($replicationProcessResult->getDestination()){
        $destination = $replicationProcessResult->getDestination();
        Common::println("Bucket replication process bucket name is: ".$destination->getBucket().PHP_EOL);
        Common::println("Bucket replication process bucket location is: ".$destination->getLocation().PHP_EOL);
        Common::println("Bucket replication process Prefix transfer type is: ".$destination->getTransferType().PHP_EOL);
    }
    if ($replicationProcessResult->getTransferType()){
        Common::println("Bucket replication process transfer type is ".$replicationProcessResult->getTransferType().PHP_EOL);
    }

    if($replicationProcessResult->getProgress()){
        $progress = $replicationProcessResult->getProgress();
        Common::println("Bucket replication process HistoricalObject is: ".$progress->getHistoricalObject().PHP_EOL);
        Common::println("Bucket replication process NewObject is: ".$progress->getNewObject().PHP_EOL);
    }
    Common::println("========================Bucket replication process end ============================".PHP_EOL);
}


// Delete Bucket replication by ID
$ossClient->deleteBucketReplication($bucket,"test_replication_1");
Common::println("Bucket replication test_replication_1 has deleted");


//******************************* For complete usage, see the following functions  ****************************************************

putBucketReplication($ossClient, $bucket);
putBucketRtc($ossClient, $bucket);
getBucketReplication($ossClient, $bucket);
deleteBucketReplication($ossClient, $bucket);
getBucketReplicationLocation($ossClient, $bucket);
getBucketReplicationProgress($ossClient, $bucket);


/**
 * Sets bucket replication rule
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 */
function putBucketReplication($ossClient, $bucket)
{
    try {
        $replicationConfig = new ReplicationConfig();

        $rule = new ReplicationRule();
        // Set the prefix of the object to be copied
        $rule->setPrefixSet('prefix_5');
        $rule->setPrefixSet('prefix_6');
        // Specify the operations that can be copied to the target bucket (ALL or PUT)
        $rule->setAction(ReplicationRule::ACTION_PUT);
        // Specify whether to copy historical data
        $rule->setHistoricalObjectReplication('enabled');
        // Which role is authorized for OSS to use for data replication
        $rule->setSyncRole('aliyunramrole');
        $destination = new ReplicationDestination();
        // Specify the target bucket to which the data will be copied。
        $destination->setBucket('test-demo5');
        // The region where the target bucket is located。
        $destination->setLocation('oss-cn-beijing');
        // Specify the data transmission link used when data is copied. internal (default) oss_acc: transmission acceleration link
        $destination->setTransferType('internal');

        $rule->addDestination($destination);
        $criteria = new ReplicationSourceSelectionCriteria();

        // Specify whether OSS copies objects created through SSE-KMS encryption
        $criteria->setStatus("Disabled");
        $rule->addSourceSelectionCriteria($criteria);
        // Specify SSE-KMS key ID. If you specify Status as Enabled, you must specify this element
        $encryption = new ReplicationEncryptionConfiguration();
        $encryption->setReplicaKmsKeyID("c4d49f85-ee30-426b-a5ed-95e9139d");
        $rule->addEncryptionConfiguration($encryption);

        $replicationConfig->addRule($rule);
        $ossClient->putBucketReplication($bucket,$replicationConfig);
        printf("Bucket replication has created");
    }catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Turn replication rules on or off
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function putBucketRtc($ossClient, $bucket)
{
    try {
        $ossClient->putBucketRtc($bucket,"disabled","test_replication_rule_id");
        printf("Bucket replication has closed");
    }catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get bucket replication rule
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketReplication($ossClient, $bucket)
{
    try {
        $replicationResult = $ossClient->getBucketReplication($bucket);
        printf("===Replication Rule  start ===");
        foreach ($replicationResult->getRules() as $rule) {
            if ($rule->getId()){
                Common::println("Replication Id:". $rule->getId().PHP_EOL);
            }
            if ($rule->getPrefixSet()){
                foreach ($rule->getPrefixSet() as $prefix){
                    Common::println("Replication Prefix: ".$prefix.PHP_EOL);
                }
            }
            if ($rule->getRTC()){
                Common::println("Replication RTC Status: ".$rule->getRTC().PHP_EOL);
            }
            if ($rule->getAction()){
                Common::println("Replication Action:". $rule->getAction().PHP_EOL);
            }

            if ($rule->getDestination()){
                Common::println("Replication Action:". $rule->getDestination()->getBucket().PHP_EOL);
                Common::println("Replication Target Bucket Location: ". $rule->getDestination()->getLocation().PHP_EOL);
                if($rule->getDestination()->getTransferType()) {
                    Common::println("Replication Target Bucket TransferType: " . $rule->getDestination()->getTransferType() . PHP_EOL);
                }
            }
            if ($rule->getSourceSelectionCriteria()){
                Common::println("Replication Source Selection Criteria Status:". $rule->getSourceSelectionCriteria()->getStatus().PHP_EOL);
            }

            if ($rule->getEncryptionConfiguration()){
                Common::println("Replication Encryption Configuration Kms Key Id:". $rule->getEncryptionConfiguration()->getReplicaKmsKeyID().PHP_EOL);
            }
            Common::println("Replication HistoricalObjectReplication:". $rule->getHistoricalObjectReplication().PHP_EOL);
            Common::println("Replication SyncRole: ". $rule->getSyncRole().PHP_EOL);
            Common::println("Replication Status: ". $rule->getStatus().PHP_EOL);
        }
        printf("===Replication Rule End ===");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get bucket replication location
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketReplicationLocation($ossClient, $bucket){
    try {
        $replicationResult = $ossClient->getBucketReplicationLocation($bucket);
        printf("=====================Bucket replication location start=================================".PHP_EOL);
        if ($replicationResult->getLocations()){
            foreach ($replicationResult->getLocations() as $location){
                printf("Bucket replication location is ".$location.PHP_EOL);
            }
        }

        if ($replicationResult->getLocationTransferTypes()){
            foreach ($replicationResult->getLocationTransferTypes() as $type){
                printf("Bucket replication location LocationTransferType location is: ".$type['location'].PHP_EOL);
                printf("Bucket replication location LocationTransferType type is: ".$type['type'].PHP_EOL);
            }
        }
        printf("========================Bucket replication location end ============================".PHP_EOL);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");

}

/**
 * Get bucket replication progress
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getBucketReplicationProgress($ossClient, $bucket){
    try {
        $result = $ossClient->getBucketReplicationProgress($bucket,"test-replication-id");
        $replicationProcessResult = $result->getRule();
        printf("=====================Bucket replication progress start=================================".PHP_EOL);
        printf("Bucket replication process id is ".$replicationProcessResult->getId().PHP_EOL);
        if($replicationProcessResult->getPrefixSet()){
            $prefixSet = $replicationProcessResult->getPrefixSet();
            foreach ($prefixSet['Prefix'] as $prefix){
                Common::println("Bucket replication process prefix is: ".$prefix.PHP_EOL);
            }
        }
        Common::println("Bucket replication process action is ".$replicationProcessResult->getAction().PHP_EOL);
        if($replicationProcessResult->getDestination()){
            $destination = $replicationProcessResult->getDestination();
            printf("Bucket replication process bucket name is: ".$destination->getBucket().PHP_EOL);
            printf("Bucket replication process bucket location is: ".$destination->getLocation().PHP_EOL);
            printf("Bucket replication process Prefix transfer type is: ".$destination->getTransferType().PHP_EOL);
        }
        if ($replicationProcessResult->getTransferType()){
            Common::println("Bucket replication process transfer type is ".$replicationProcessResult->getTransferType().PHP_EOL);
        }
        printf("Bucket replication process status is ".$replicationProcessResult->getStatus().PHP_EOL);
        printf("Bucket replication process historicalObjectReplication is: ".$replicationProcessResult->getHistoricalObjectReplication().PHP_EOL);

        if($replicationProcessResult->getProgress()){
            $progress = $replicationProcessResult->getProgress();
            printf("Bucket replication process HistoricalObject is: ".$progress->getHistoricalObject().PHP_EOL);
            printf("Bucket replication process NewObject is: ".$progress->getNewObject().PHP_EOL);
        }
        printf("========================Bucket replication progress end ============================".PHP_EOL);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Delete bucket replication rule
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketReplication($ossClient, $bucket)
{
    $ruleId = 'test_replication_id';
    try {
        $ossClient->deleteBucketReplication($bucket,$ruleId);
        printf("$bucket replication rule has deleted");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

