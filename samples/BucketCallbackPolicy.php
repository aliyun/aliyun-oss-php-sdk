<?php

require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\CallbackPolicyConfig;
use OSS\Model\CallbackPolicyItem;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

$config = new CallbackPolicyConfig();
$name = "first";
$callback = base64_encode('{"callbackUrl":"http://www.aliyuncs.com", "callbackBody":"bucket=${bucket}&object=${object}"}');

$policyItem = new CallbackPolicyItem($name,$callback);
$config->addPolicyItem($policyItem);

$name1 = "second";
$callback1 = base64_encode('{"callbackUrl":"http://www.aliyun.com", "callbackBody":"bucket=${bucket}&object=${object}"}');
$callbackVar1 = base64_encode('{"x:a":"a", "x:b":"b"}');

$policyItem1 = new CallbackPolicyItem($name1,$callback1,$callbackVar1);
$config->addPolicyItem($policyItem1);

$ossClient->putBucketCallbackPolicy($bucket, $config);
Common::println("bucket $bucket callback policy created");

$rs = $ossClient->getBucketCallbackPolicy($bucket);

foreach ($rs->getPolicyItem() as $policyItem){
    printf("Callback Policy Name:%s" . "\n",$policyItem->getPolicyName());
    printf("Callback Policy Callback:%s" . "\n",base64_decode($policyItem->getCallback()));
    if ($policyItem->getCallbackVar() != ""){
        printf("Callback Policy Callback Var:%s" . "\n",base64_decode($policyItem->getCallbackVar()));
    }

}

$config = $ossClient->deleteBucketCallbackPolicy($bucket);
Common::println("bucket $bucket callback policy has deleted");

//******************************* For complete usage, see the following functions ****************************************************
putBucketCallbackPolicy($ossClient, $bucket);
getBucketCallbackPolicy($ossClient, $bucket);
deleteBucketCallbackPolicy($ossClient, $bucket);

/**
 * Set Bucket Callback Policy Config
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws \OSS\Http\RequestCore_Exception
 */

function putBucketCallbackPolicy($ossClient,$bucket){
    try {
        $config = new CallbackPolicyConfig();
        $name = "first";
        $callback = base64_encode('{"callbackUrl":"http://www.aliyuncs.com", "callbackBody":"bucket=${bucket}&object=${object}"}');

        $policyItem = new CallbackPolicyItem($name,$callback);
        $config->addPolicyItem($policyItem);

        $name1 = "second";
        $callback1 = base64_encode('{"callbackUrl":"http://www.aliyun.com", "callbackBody":"bucket=${bucket}&object=${object}"}');
        $callbackVar1 = base64_encode('{"x:a":"a", "x:b":"b"}');

        $policyItem1 = new CallbackPolicyItem($name1,$callback1,$callbackVar1);
        $config->addPolicyItem($policyItem1);
        $ossClient->putBucketCallbackPolicy($bucket, $config);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Bucket Callback Policy
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws \OSS\Http\RequestCore_Exception
 */
function getBucketCallbackPolicy($ossClient,$bucket){
    try {
        $rs = $ossClient->getBucketCallbackPolicy($bucket);
        foreach ($rs->getPolicyItem() as $policyItem){
            printf("Callback Policy Name:%s" . "\n",$policyItem->getPolicyName());
            printf("Callback Policy Callback:%s" . "\n",base64_decode($policyItem->getCallback()));
            if ($policyItem->getCallbackVar() != ""){
                printf("Callback Policy Callback Var:%s" . "\n",base64_decode($policyItem->getCallbackVar()));
            }

        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Delete Bucket Callback Policy
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 * @throws \OSS\Http\RequestCore_Exception
 */

function deleteBucketCallbackPolicy($ossClient,$bucket){
    try {
        $ossClient->deleteBucketCallbackPolicy($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

