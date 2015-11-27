<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\CorsConfig;
use OSS\Model\CorsRule;

$ossClient = Common::getOssClient();
if(is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();


//******************************* 简单使用 ****************************************************************

// 设置cors配置
$corsConfig = new CorsConfig();
$rule = new CorsRule();
$rule->addAllowedHeader("x-oss-test");
$rule->addAllowedOrigin("http://www.b.com");
$rule->addAllowedMethod("POST");
$rule->setMaxAgeSeconds(10);
$corsConfig->addRule($rule);
$ossClient->putBucketCors($bucket, $corsConfig);
Common::println("bucket $bucket corsConfig created:" . $corsConfig->serializeToXml());

// 获取cors配置
$corsConfig = $ossClient->getBucketCors($bucket);
Common::println("bucket $bucket corsConfig fetched:" . $corsConfig->serializeToXml());

// 删除cors配置
$ossClient->deleteBucketCors($bucket);
Common::println("bucket $bucket corsConfig deleted");

//******************************* 完整用法参考下面函数  *****************************************************

putBucketCors($ossClient, $bucket);
getBucketCors($ossClient, $bucket);
deleteBucketCors($ossClient, $bucket);
getBucketCors($ossClient, $bucket);

/**
 * 设置bucket的cors配置
 *
 * @param OssClient $ossClient OSSClient实例
 * @param string    $bucket 存储空间名称
 * @return null
 */
function putBucketCors($ossClient, $bucket)
{
    $corsConfig = new CorsConfig();
    $rule = new CorsRule();
    $rule->addAllowedHeader("x-oss-test");
    $rule->addAllowedHeader("x-oss-test2");
    $rule->addAllowedHeader("x-oss-test2");
    $rule->addAllowedHeader("x-oss-test3");
    $rule->addAllowedOrigin("http://www.b.com");
    $rule->addAllowedOrigin("http://www.a.com");
    $rule->addAllowedOrigin("http://www.a.com");
    $rule->addAllowedMethod("GET");
    $rule->addAllowedMethod("PUT");
    $rule->addAllowedMethod("POST");
    $rule->addExposeHeader("x-oss-test1");
    $rule->addExposeHeader("x-oss-test1");
    $rule->addExposeHeader("x-oss-test2");
    $rule->setMaxAgeSeconds(10);
    $corsConfig->addRule($rule);
    $rule = new CorsRule();
    $rule->addAllowedHeader("x-oss-test");
    $rule->addAllowedMethod("GET");
    $rule->addAllowedOrigin("http://www.b.com");
    $rule->addExposeHeader("x-oss-test1");
    $rule->setMaxAgeSeconds(110);
    $corsConfig->addRule($rule);

    try{
        $ossClient->putBucketCors($bucket, $corsConfig);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * 获取并打印bucket的cors配置
 *
 * @param OssClient $ossClient OSSClient实例
 * @param string    $bucket 存储空间名称
 * @return null
 */
function getBucketCors($ossClient, $bucket)
{
    $corsConfig = null;
    try{
        $corsConfig = $ossClient->getBucketCors($bucket);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($corsConfig->serializeToXml() . "\n");
}

/**
 * 删除bucket的所有的cors配置
 *
 * @param OssClient $ossClient OSSClient实例
 * @param string    $bucket 存储空间名称
 * @return null
 */
function deleteBucketCors($ossClient, $bucket)
{
    try{
        $ossClient->deleteBucketCors($bucket);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

