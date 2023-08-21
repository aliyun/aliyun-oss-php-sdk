<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\LifecycleRule;
use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleExpiration;
use OSS\Model\LifecycleAbortMultipartUpload;
use OSS\Model\LifecycleTag;
use OSS\Model\LifecycleTransition;
use OSS\Model\LifecycleNoncurrentVersionTransition;
use OSS\Model\LifecycleNot;
use OSS\Model\LifecycleFilter;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************

// Set lifecycle configuration
$lifecycleConfig = new LifecycleConfig();

// rule 1: the lifecycle rule ID is' rule1 ', the prefix is' logs/', and the status is' Enabled '
$rule1 = new LifecycleRule("rule1", "logs/", LifecycleRule::STATUS_ENANLED);
$lifecycleExpiration = new LifecycleExpiration();
//set expiration time
$lifecycleExpiration->setDays(3);
$rule1->setExpiration($lifecycleExpiration);

// The expired attributes that have not been uploaded will take effect one day after the object is last updated.
$lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
$lifecycleAbortMultipartUpload->setDays(1);
$rule1->setAbortMultipartUpload($lifecycleAbortMultipartUpload);

$lifecycleConfig->addRule($rule1);

// rule 2:  set id,prefix,status
$rule2 = new LifecycleRule("rule2", "logs2/", LifecycleRule::STATUS_ENANLED);
// Convert the object storage type to AI within 30 days
$lifecycleTransition = new LifecycleTransition();
$lifecycleTransition->setDays(30);
$lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
$rule2->addTransition($lifecycleTransition);
// Convert the object storage type to Archive within 60 days
$lifecycleTransition = new LifecycleTransition();
$lifecycleTransition->setDays(60);
$lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_ARCHIVE);
$rule2->addTransition($lifecycleTransition);

$lifecycleExpiration = new LifecycleExpiration();
// The specified lifecycle rule takes effect 180 days after the object is last updated.
$lifecycleExpiration->setDays(180);
$rule2->setExpiration($lifecycleExpiration);
$lifecycleConfig->addRule($rule2);

// rule 3: add expiration CreatedBeforeDate and AbortMultipartUpload CreatedBeforeDate
$rule3 = new LifecycleRule("rule3", "logs3/", LifecycleRule::STATUS_ENANLED);
$lifecycleExpiration = new LifecycleExpiration();
// The last update time is earlier than 2017-01-01T00:00:00.000Z object to be overdue
$lifecycleExpiration->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
$rule3->setExpiration($lifecycleExpiration);

// The last update time is earlier than 2017-01-01T00:00:00.000Z Multipart to be overdue
$lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
$lifecycleAbortMultipartUpload->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
$rule3->setAbortMultipartUpload($lifecycleAbortMultipartUpload);
$lifecycleConfig->addRule($rule3);

// rule 4:  add two tags
$rule4 = new LifecycleRule("rule4", "logs4/", LifecycleRule::STATUS_ENANLED);

$tag = new LifecycleTag();
$tag->setKey("key1");
$tag->setValue("val1");
$rule4->addTag($tag);

$tag2 = new LifecycleTag();
$tag2->setKey("key12");
$tag2->setValue("val12");
$rule4->addTag($tag2);
$lifecycleConfig->addRule($rule4);
$lifecycleTransition = new LifecycleTransition();
$lifecycleTransition->setDays(30);
$lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
$rule4->addTransition($lifecycleTransition);


// rule 5:  transition add IsAccessTime false
$rule5 = new LifecycleRule("rule5", "logs5/", LifecycleRule::STATUS_ENANLED);

$lifecycleTransition = new LifecycleTransition();
$lifecycleTransition->setDays(30);
$lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
$lifecycleTransition->setIsAccessTime(false);
$rule5->addTransition($lifecycleTransition);
$lifecycleConfig->addRule($rule5);

// rule 6:  set id,prefix,status  transition add IsAccessTime,ReturnToStdWhenVisit
$rule6 = new LifecycleRule("rule6", "logs6/", LifecycleRule::STATUS_ENANLED);

$lifecycleTransition = new LifecycleTransition();
$lifecycleTransition->setDays(30);
$lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
$lifecycleTransition->setIsAccessTime(true);
$lifecycleTransition->setReturnToStdWhenVisit(false);
$rule6->addTransition($lifecycleTransition);
$lifecycleConfig->addRule($rule6);

// rule 7:  set id,prefix,status  NoncurrentVersionExpiration addIsAccessTime,ReturnToStdWhenVisit
$rule7 = new LifecycleRule("rule7", "logs7/", LifecycleRule::STATUS_ENANLED);

$nonTransition = new LifecycleNoncurrentVersionTransition();
$nonTransition->setNoncurrentDays(30);
$nonTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
$nonTransition->setIsAccessTime(true);
$nonTransition->setReturnToStdWhenVisit(true);
$rule7->addNoncurrentVersionTransition($nonTransition);
$lifecycleConfig->addRule($rule7);
$ossClient->putBucketLifecycle($bucket, $lifecycleConfig);
Common::println("bucket $bucket lifecycleConfig created:" . $lifecycleConfig->serializeToXml());

// Set Lifecycle Rule With Filter
$lifecycleConfig = new LifecycleConfig();
$rule = new LifecycleRule("rule-filter", "logs", LifecycleRule::STATUS_ENANLED);
$lifecycleTransition = new LifecycleTransition();
$lifecycleTransition->setDays(30);
$lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
$expiration = new LifecycleExpiration(100,null);

$rule->addTransition($lifecycleTransition);
$rule->setExpiration($expiration);
$not = new LifecycleNot();
$tag = new LifecycleTag();
$tag->setKey("key1");
$tag->setValue("val1");
$not->setTag($tag);
$not->setPrefix("logs1/");
$filter = new LifecycleFilter();
$filter->addNot($not);
$rule->setFilter($filter);
$lifecycleConfig->addRule($rule);
$ossClient->putBucketLifecycle($bucket, $lifecycleConfig);
Common::println("bucket $bucket lifecycleConfig created:" . $lifecycleConfig->serializeToXml());


// Get lifecycle configuration
$lifecycleConfig = $ossClient->getBucketLifecycle($bucket);
Common::println("bucket $bucket lifecycleConfig fetched:" . $lifecycleConfig->serializeToXml());

// print lifecycle config
$rules = $lifecycleConfig->getRules();
foreach ($rules as $rule){
    printf("=====================".$rule->getId()."====info================".PHP_EOL);
    printf("Rule Id:".$rule->getId().PHP_EOL);
    printf("Rule Status:".$rule->getStatus().PHP_EOL);
    printf("Rule Prefix:".$rule->getPrefix().PHP_EOL);

    // print expiration
    $expiration = $rule->getExpiration();
    if (isset($expiration)){
        if ($expiration->getDays()){
            printf("Rule Expiration Days:".$expiration->getDays().PHP_EOL);
        }
        if ($expiration->getDate()){
            printf("Rule Expiration Date:".$expiration->getDate().PHP_EOL);
        }
        if ($expiration->getCreatedBeforeDate()){
            printf("Rule Expiration Created Before Date:".$expiration->getCreatedBeforeDate().PHP_EOL);
        }
        if ($expiration->getExpiredObjectDeleteMarker()){
            printf("Rule Expiration Expired Object Delete Marker:".$expiration->getExpiredObjectDeleteMarker().PHP_EOL);
        }
    }

    // print tag
    $tags = $rule->getTag();
    if (isset($tags)){
        foreach ($tags as $tag){
            printf("Rule Tag Key:".$tag->getKey().PHP_EOL);
            printf("Rule Tag Value:".$tag->getValue().PHP_EOL);
        }
    }
    // print transition
    $transitions = $rule->getTransition();
    if (isset($transitions)){
        foreach ($transitions as $transition){
            printf("Rule Transition Days:".$transition->getDays().PHP_EOL);
            printf("Rule Transition Created Before Date:".$transition->getCreatedBeforeDate().PHP_EOL);
            printf("Rule Transition Storage Class:".$transition->getStorageClass().PHP_EOL);
            printf("Rule Transition Is Access Time:".$transition->getIsAccessTime().PHP_EOL);
            printf("Rule Transition Return To Std When Visit:".$transition->getReturnToStdWhenVisit().PHP_EOL);
            printf("Rule Transition Allow Small File:".$transition->getAllowSmallFile().PHP_EOL);
        }
    }

    //print Abort Multipart Upload
    $abortMultipartUpload = $rule->getAbortMultipartUpload();
    if (isset($abortMultipartUpload)){
        if ($abortMultipartUpload->getDays()){
            printf("Rule Abort Multipart Upload Days:".$abortMultipartUpload->getDays().PHP_EOL);
        }

        if ($abortMultipartUpload->getCreatedBeforeDate()){
            printf("Rule Abort Multipart Upload Created Before Date:".$abortMultipartUpload->getCreatedBeforeDate().PHP_EOL);
        }

    }

    //print Noncurrent Version Transition
    $nonVersionTransitions = $rule->getNoncurrentVersionTransition();
    if (isset($nonVersionTransitions)){
        foreach ($nonVersionTransitions as $nonVersionTransition){
            printf("Rule Non Version Transition Non Current Days:".$nonVersionTransition->getNoncurrentDays().PHP_EOL);
            printf("Rule Non Version Transition Storage Class:".$nonVersionTransition->getStorageClass().PHP_EOL);
            printf("Rule Non Version Transition Is Access Time:".$nonVersionTransition->getIsAccessTime().PHP_EOL);
            printf("Rule Non Version Transition Return To Std When Visit:".$nonVersionTransition->getReturnToStdWhenVisit().PHP_EOL);
            printf("Rule Non Version Transition Allow Small File:".$nonVersionTransition->getAllowSmallFile().PHP_EOL);
        }
    }

    $filter = $rule->getFilter();
    if (isset($filter)){
        foreach ($filter->getNot() as $not){
            if ($not->getPrefix()) {
                printf("Rule Filter Not Prefix:".$not->getTag()->getKey().PHP_EOL);
            }

            if ($not->getTag()->getKey()) {
                printf("Rule Filter Not Tag Key:".$not->getTag()->getKey().PHP_EOL);
            }

            if ($not->getTag()->getValue()) {
                printf("Rule Filter Not Tag Value:".$not->getTag()->getValue().PHP_EOL);
            }
        }
    }

}

// Delete bucket lifecycle configuration
$ossClient->deleteBucketLifecycle($bucket);
Common::println("bucket $bucket lifecycleConfig deleted");


//***************************** For complete usage, see the following functions  ***********************************************

putBucketLifecycle($ossClient, $bucket);
getBucketLifecycle($ossClient, $bucket);
deleteBucketLifecycle($ossClient, $bucket);
getBucketLifecycle($ossClient, $bucket);

/**
 * Set bucket lifecycle configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketLifecycle($ossClient, $bucket)
{
    // Set lifecycle configuration
    $lifecycleConfig = new LifecycleConfig();

    // rule 1: the lifecycle rule ID is' rule1 ', the prefix is' logs/', and the status is' Enabled '
    $rule1 = new LifecycleRule("rule1", "logs/", LifecycleRule::STATUS_ENANLED);
    $lifecycleExpiration = new LifecycleExpiration();
    //set expiration time
    $lifecycleExpiration->setDays(3);
    $rule1->setExpiration($lifecycleExpiration);

    // The expired attributes that have not been uploaded will take effect one day after the object is last updated.
    $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
    $lifecycleAbortMultipartUpload->setDays(1);
    $rule1->setAbortMultipartUpload($lifecycleAbortMultipartUpload);

    $lifecycleConfig->addRule($rule1);

    // rule 2:   the lifecycle rule ID is' rule2 ', the prefix is' logs2/', and the status is' Enabled '
    $rule2 = new LifecycleRule("rule2", "logs2/", LifecycleRule::STATUS_ENANLED);
    // Convert the object storage type to AI within 30 days
    $lifecycleTransition = new LifecycleTransition();
    $lifecycleTransition->setDays(30);
    $lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
    $rule2->addTransition($lifecycleTransition);
    // Convert the object storage type to Archive within 60 days
    $lifecycleTransition = new LifecycleTransition();
    $lifecycleTransition->setDays(60);
    $lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_ARCHIVE);
    $rule2->addTransition($lifecycleTransition);

    $lifecycleExpiration = new LifecycleExpiration();
    // The specified lifecycle rule takes effect 180 days after the object is last updated.
    $lifecycleExpiration->setDays(180);
    $rule2->setExpiration($lifecycleExpiration);
    $lifecycleConfig->addRule($rule2);

    // rule 3
    $rule3 = new LifecycleRule("rule3", "logs3/", LifecycleRule::STATUS_ENANLED);
    $lifecycleExpiration = new LifecycleExpiration();
    // The last update time is earlier than 2017-01-01T00:00:00.000Z object to be overdue
    $lifecycleExpiration->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
    $rule3->setExpiration($lifecycleExpiration);

    // The last update time is earlier than 2017-01-01T00:00:00.000Z Multipart to be overdue
    $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
    $lifecycleAbortMultipartUpload->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
    $rule3->setAbortMultipartUpload($lifecycleAbortMultipartUpload);
    $lifecycleConfig->addRule($rule3);

    // rule 4:  add two tags
    $rule4 = new LifecycleRule("rule4", "logs4/", LifecycleRule::STATUS_ENANLED);

    $tag = new LifecycleTag();
    $tag->setKey("key1");
    $tag->setValue("val1");
    $rule4->addTag($tag);
    $tag2 = new LifecycleTag();
    $tag2->setKey("key12");
    $tag2->setValue("val12");
    $rule4->addTag($tag2);
    $lifecycleConfig->addRule($rule4);
    $lifecycleTransition = new LifecycleTransition();
    $lifecycleTransition->setDays(30);
    $lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
    $rule4->addTransition($lifecycleTransition);

    // rule 5:
    $rule5 = new LifecycleRule("rule5", "logs5/", LifecycleRule::STATUS_ENANLED);

    $lifecycleTransition = new LifecycleTransition();
    $lifecycleTransition->setDays(30);
    $lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
    $lifecycleTransition->setIsAccessTime(false);
    $rule5->addTransition($lifecycleTransition);
    $lifecycleConfig->addRule($rule5);

    // rule 6t
    $rule6 = new LifecycleRule("rule6", "logs6/", LifecycleRule::STATUS_ENANLED);

    $lifecycleTransition = new LifecycleTransition();
    $lifecycleTransition->setDays(30);
    $lifecycleTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
    $lifecycleTransition->setIsAccessTime(true);
    $lifecycleTransition->setReturnToStdWhenVisit(false);
    $rule6->addTransition($lifecycleTransition);
    $lifecycleConfig->addRule($rule6);

    // rule 7
    $rule7 = new LifecycleRule("rule7", "logs7/", LifecycleRule::STATUS_ENANLED);

    $nonTransition = new LifecycleNoncurrentVersionTransition();
    $nonTransition->setNoncurrentDays(30);
    $nonTransition->setStorageClass($ossClient::OSS_STORAGE_IA);
    $nonTransition->setIsAccessTime(true);
    $nonTransition->setReturnToStdWhenVisit(true);
    $rule7->addNoncurrentVersionTransition($nonTransition);
    try {
        $ossClient->putBucketLifecycle($bucket, $lifecycleConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket lifecycle configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketLifecycle($ossClient, $bucket)
{
    try {
        $lifecycleConfig = $ossClient->getBucketLifecycle($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    print(__FUNCTION__ . ": OK" . "\n");
    print($lifecycleConfig->serializeToXml() . "\n");
    // print lifecycle config
    $rules = $lifecycleConfig->getRules();
    foreach ($rules as $rule){
        printf("=====================".$rule->getId()."====info================".PHP_EOL);
        printf("Rule Id:".$rule->getId().PHP_EOL);
        printf("Rule Status:".$rule->getStatus().PHP_EOL);
        printf("Rule Prefix:".$rule->getPrefix().PHP_EOL);

        // print expiration
        $expiration = $rule->getExpiration();
        if (isset($expiration)){
            if ($expiration->getDays()){
                printf("Rule Expiration Days:".$expiration->getDays().PHP_EOL);
            }
            if ($expiration->getDate()){
                printf("Rule Expiration Date:".$expiration->getDate().PHP_EOL);
            }
            if ($expiration->getCreatedBeforeDate()){
                printf("Rule Expiration Created Before Date:".$expiration->getCreatedBeforeDate().PHP_EOL);
            }
            if ($expiration->getExpiredObjectDeleteMarker()){
                printf("Rule Expiration Expired Object Delete Marker:".$expiration->getExpiredObjectDeleteMarker().PHP_EOL);
            }
        }

        // print tag
        $tags = $rule->getTag();
        if (isset($tags)){
            foreach ($tags as $tag){
                printf("Rule Tag Key:".$tag->getKey().PHP_EOL);
                printf("Rule Tag Value:".$tag->getValue().PHP_EOL);
            }
        }
        // print transition
        $transitions = $rule->getTransition();
        if (isset($transitions)){
            foreach ($transitions as $transition){
                printf("Rule Transition Days:".$transition->getDays().PHP_EOL);
                printf("Rule Transition Created Before Date:".$transition->getCreatedBeforeDate().PHP_EOL);
                printf("Rule Transition Storage Class:".$transition->getStorageClass().PHP_EOL);
                printf("Rule Transition Is Access Time:".$transition->getIsAccessTime().PHP_EOL);
                printf("Rule Transition Return To Std When Visit:".$transition->getReturnToStdWhenVisit().PHP_EOL);
                printf("Rule Transition Allow Small File:".$transition->getAllowSmallFile().PHP_EOL);
            }
        }

        //print Abort Multipart Upload
        $abortMultipartUpload = $rule->getAbortMultipartUpload();
        if (isset($abortMultipartUpload)){
            if ($abortMultipartUpload->getDays()){
                printf("Rule Abort Multipart Upload Days:".$abortMultipartUpload->getDays().PHP_EOL);
            }

            if ($abortMultipartUpload->getCreatedBeforeDate()){
                printf("Rule Abort Multipart Upload Created Before Date:".$abortMultipartUpload->getCreatedBeforeDate().PHP_EOL);
            }

        }

        //print Noncurrent Version Transition
        $nonVersionTransitions = $rule->getNoncurrentVersionTransition();
        if (isset($nonVersionTransitions)){
            foreach ($nonVersionTransitions as $nonVersionTransition){
                printf("Rule Non Version Transition Non Current Days:".$nonVersionTransition->getNoncurrentDays().PHP_EOL);
                printf("Rule Non Version Transition Storage Class:".$nonVersionTransition->getStorageClass().PHP_EOL);
                printf("Rule Non Version Transition Is Access Time:".$nonVersionTransition->getIsAccessTime().PHP_EOL);
                printf("Rule Non Version Transition Return To Std When Visit:".$nonVersionTransition->getReturnToStdWhenVisit().PHP_EOL);
                printf("Rule Non Version Transition Allow Small File:".$nonVersionTransition->getAllowSmallFile().PHP_EOL);
            }
        }

        $filter = $rule->getFilter();
        if (isset($filter)){
            foreach ($filter->getNot() as $not){
                if ($not->getPrefix()) {
                    printf("Rule Filter Not Prefix:".$not->getTag()->getKey().PHP_EOL);
                }

                if ($not->getTag()->getKey()) {
                    printf("Rule Filter Not Tag Key:".$not->getTag()->getKey().PHP_EOL);
                }

                if ($not->getTag()->getValue()) {
                    printf("Rule Filter Not Tag Value:".$not->getTag()->getValue().PHP_EOL);
                }
            }
        }

    }
}

/**
 * Delete bucket lifecycle configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketLifecycle($ossClient, $bucket)
{
    try {
        $ossClient->deleteBucketLifecycle($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


