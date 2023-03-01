<?php

require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\MetaQuery;
use OSS\Model\MetaQueryAggregation;
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage****************************************************************

// Open Meta Query
$ossClient->openMetaQuery($bucket);
Common::println("bucket $bucket open meta query success".PHP_EOL);

// Get Meta Query Status
$rs = $ossClient->getMetaQueryStatus($bucket);
Common::println("bucket $bucket meta query status state:".$rs->getState().PHP_EOL);
Common::println("bucket $bucket meta query status phase:".$rs->getPhase().PHP_EOL);
Common::println("bucket $bucket meta query status create time:".$rs->getCreateTime().PHP_EOL);
Common::println("bucket $bucket meta query status update time:".$rs->getUpdateTime().PHP_EOL);

// Do meta query
// Query files larger than 500
$query = '{"Field": "Size","Value": "500","Operation": "gt"}';
$maxResults=100;
$sort="Size";
$order="asc";
$metaQuery = new MetaQuery();
$metaQuery->setQuery($query);
$metaQuery->setMaxResults($maxResults);
$metaQuery->setSort($sort);
$metaQuery->setOrder($order);
//Add aggregation condition
$agg = new MetaQueryAggregation();
$agg->setField("Size");
$agg->setOperation("sum");
$aggOne = new MetaQueryAggregation();
$aggOne->setField("Size");
$aggOne->setOperation("count");
$metaQuery->addAggregation($agg);
$metaQuery->addAggregation($aggOne);

$rs = $ossClient->doMetaQuery($bucket,$metaQuery);
if ($rs->getNextToken() != ""){
    printf("Next Token:".$rs->getNextToken().PHP_EOL);
}
if ($rs->getFiles() != null){
    foreach ($rs->getFiles() as $file ){
        printf("File info ===============".$file->getFileName()."============================== start ".PHP_EOL);
        printf("File Name:".$file->getFileName().PHP_EOL);
        printf("File Size:".$file->getSize().PHP_EOL);
        printf("File Modified Time:".$file->getFileModifiedTime().PHP_EOL);
        printf("File Oss Object Type:".$file->getOssObjectType().PHP_EOL);
        printf("File Oss Storage Class:".$file->getOssStorageClass().PHP_EOL);
        printf("File Object Acl:".$file->getObjectAcl().PHP_EOL);
        printf("File ETag:".$file->getETag().PHP_EOL);
        printf("File Oss Crc64:".$file->getOssCrc64().PHP_EOL);
        printf("File Oss Tagging Count:".$file->getOssTaggingCount().PHP_EOL);
        printf("File Server Side Encryption:".$file->getServerSideEncryption().PHP_EOL);
        printf("File Server Side Encryption Customer Algorithm:".$file->getServerSideEncryptionCustomerAlgorithm().PHP_EOL);
        if ($file->getOssUserMeta() != null){
            foreach ($file->getOssUserMeta() as $ossUserMeta ){
                printf("File OSS User Meta Key:".$ossUserMeta->getKey().PHP_EOL);
                printf("File OSS User Meta Value:".$ossUserMeta->getValue().PHP_EOL);
            }
        }
        if ($file->getOssTagging() != null){
            foreach ($file->getOssTagging() as $ossTagging ){
                printf("File OSS Tagging Key:".$ossTagging->getKey().PHP_EOL);
                printf("File OSS Tagging Value:".$ossTagging->getValue().PHP_EOL);
            }
        }
    }
}
if ($rs->getAggregations() != null){
    foreach ($rs->getAggregations() as $aggregation){
        printf("Aggregation Field:".$aggregation->getField().PHP_EOL);
        printf("Aggregation Operation:".$aggregation->getOperation().PHP_EOL);
        printf("Aggregation Value:".$aggregation->getValue().PHP_EOL);
        if ($aggregation->getGroups() != null){
            foreach ($aggregation->getGroups() as $group ){
                printf("Aggregation Group Value:".$group->getValue().PHP_EOL);
                printf("Aggregation Group Count:".$group->getCount().PHP_EOL);
            }
        }
    }
}

// Close Meta Query
$ossClient->closeMetaQuery($bucket);
Common::println("bucket $bucket has closed meta query".PHP_EOL);

//******************************* For complete usage, see the following functions ****************************************************
openMetaQuery($ossClient,$bucket);
getMetaQueryStatus($ossClient,$bucket);
doMetaQuery($ossClient,$bucket);
closeMetaQuery($ossClient,$bucket);

/**
 * Open Meta Query
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function openMetaQuery($ossClient,$bucket){
    try {
        $ossClient->openMetaQuery($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Meta Query Status
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getMetaQueryStatus($ossClient,$bucket){
    try {
        $rs = $ossClient->getMetaQueryStatus($bucket);
        printf("bucket $bucket meta query status state:".$rs->getState().PHP_EOL);
        printf("bucket $bucket meta query status phase:".$rs->getPhase().PHP_EOL);
        printf("bucket $bucket meta query status create time:".$rs->getCreateTime().PHP_EOL);
        printf("bucket $bucket meta query status update time:".$rs->getUpdateTime().PHP_EOL);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Do Meta Query
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function doMetaQuery($ossClient,$bucket){
    try {
        // Query files larger than 500
        $query = '{"Field": "Size","Value": "500","Operation": "gt"}';
        $maxResults=100;
        $sort="Size";
        $order="asc";
        $metaQuery = new MetaQuery($query,$maxResults,$sort,$order);
        //Add aggregation condition
        $agg = new MetaQueryAggregation("Size","sum");
        $aggOne = new MetaQueryAggregation("Size","count");
        $metaQuery->addAggregation($agg);
        $metaQuery->addAggregation($aggOne);
        $rs = $ossClient->doMetaQuery($bucket,$metaQuery);
        if ($rs->getNextToken() != ""){
            printf("Next Token:".$rs->getNextToken().PHP_EOL);
        }
        if ($rs->getFiles() != null){
            foreach ($rs->getFiles() as $file ){
                printf("File info ===============".$file->getFileName()."============================== start ".PHP_EOL);
                printf("File Name:".$file->getFileName().PHP_EOL);
                printf("File Size:".$file->getSize().PHP_EOL);
                printf("File Modified Time:".$file->getFileModifiedTime().PHP_EOL);
                printf("File Oss Object Type:".$file->getOssObjectType().PHP_EOL);
                printf("File Oss Storage Class:".$file->getOssStorageClass().PHP_EOL);
                printf("File Object Acl:".$file->getObjectAcl().PHP_EOL);
                printf("File ETag:".$file->getETag().PHP_EOL);
                printf("File Oss Crc64:".$file->getOssCrc64().PHP_EOL);
                printf("File Oss Tagging Count:".$file->getOssTaggingCount().PHP_EOL);
                printf("File Server Side Encryption:".$file->getServerSideEncryption().PHP_EOL);
                printf("File Server Side Encryption Customer Algorithm:".$file->getServerSideEncryptionCustomerAlgorithm().PHP_EOL);
                if ($file->getOssUserMeta() != null){
                    foreach ($file->getOssUserMeta() as $ossUserMeta ){
                        printf("File OSS User Meta Key:".$ossUserMeta->getKey().PHP_EOL);
                        printf("File OSS User Meta Value:".$ossUserMeta->getValue().PHP_EOL);
                    }
                }
                if ($file->getOssTagging() != null){
                    foreach ($file->getOssTagging() as $ossTagging ){
                        printf("File OSS Tagging Key:".$ossTagging->getKey().PHP_EOL);
                        printf("File OSS Tagging Value:".$ossTagging->getValue().PHP_EOL);
                    }
                }
            }
        }
        if ($rs->getAggregations() != null){
            foreach ($rs->getAggregations() as $aggregation){
                printf("Aggregation Field:".$aggregation->getField().PHP_EOL);
                printf("Aggregation Operation:".$aggregation->getOperation().PHP_EOL);
                printf("Aggregation Value:".$aggregation->getValue().PHP_EOL);
                if ($aggregation->getGroups() != null){
                    foreach ($aggregation->getGroups() as $group ){
                        printf("Aggregation Group Value:".$group->getValue().PHP_EOL);
                        printf("Aggregation Group Count:".$group->getCount().PHP_EOL);
                    }
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

/**
 * Close Meta Query
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function closeMetaQuery($ossClient,$bucket){
    try {
        $ossClient->closeMetaQuery($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}