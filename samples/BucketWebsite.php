<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\WebsiteConfig;
use OSS\Model\WebsiteRoutingRule;
use OSS\Model\WebsiteCondition;
use OSS\Model\WebsiteRedirect;
use OSS\Model\WebsiteMirrorHeaders;
use OSS\Model\WebsiteIncludeHeader;
use OSS\Model\WebsiteIndexDocument;
use OSS\Model\WebsiteErrorDocument;
use OSS\Model\WebsiteMirrorHeadersSet;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage ***************************************************************

// Set bucket static website configuration
$index = new WebsiteIndexDocument("index.html");
$error = new WebsiteErrorDocument("error.html");
//$websiteConfig = new WebsiteConfig($index, $error);
$websiteConfig = new WebsiteConfig($index, $error);
$ossClient->putBucketWebsite($bucket, $websiteConfig);
Common::println("bucket $bucket websiteConfig created:" . $websiteConfig->serializeToXml());

// Get bucket static website configuration
$websiteConfig = $ossClient->getBucketWebsite($bucket);
Common::println("bucket $bucket websiteConfig fetched:" . $websiteConfig->serializeToXml());

// Delete bucket static website configuration
$ossClient->deleteBucketWebsite($bucket);
Common::println("bucket $bucket websiteConfig deleted");

// Set Mirror back to source configuration
$index->setSupportSubDir(false);
$index->setType(0);
$error->setHttpStatus(404);

$websiteConfig = new WebsiteConfig($index, $error);

$routingRule = new WebsiteRoutingRule();

$routingRule->setNumber(1);
$websiteCondition = new WebsiteCondition();
$websiteCondition->setKeyPrefixEquals("examplebucket");
$websiteCondition->setHttpErrorCodeReturnedEquals(404);

$websiteRedirect = new WebsiteRedirect();
$websiteRedirect->setRedirectType(WebsiteRedirect::MIRROR);
$websiteRedirect->setMirrorURL('https://www.example.com/');
$websiteRedirect->setPassQueryString(true);
$websiteRedirect->setMirrorPassQueryString(true);
// Specifies the status code returned when jumping
//$websiteRedirect->setHttpRedirectCode(302);
// Specifies the domain name at jump time
//$websiteRedirect->setHostName("oss.aliyuncs.com");
// Specifies the protocol at jump time. It takes effect only when redirecttype is set to external or alicdn.
//$websiteRedirect->setProtocol(WebsiteRedirect::HTTPS);
// During redirect, the object name will be replaced with the value specified by replacekeywith. Replacekeywith supports setting variables.
$websiteRedirect->setReplaceKeyWith('${key}.jpg');
// If this field is set to true, the prefix of the object will be replaced with the value specified by replacekeyprefixwith.
$websiteRedirect->setEnableReplacePrefix(false);
// When redirect, the prefix of the object name will be replaced with this value.
//$websiteRedirect->setReplaceKeyPrefixWith('examplebucket');
// Check MD5 of the source body
$websiteRedirect->setMirrorCheckMd5(true);

$mirrorHeaders = new WebsiteMirrorHeaders();
// Whether to transparently transmit headers other than the following headers to the source station

$mirrorHeaders->setPassAll(true);

$pass = 'cache-control-one';
$passOne = 'pass-one';
$mirrorHeaders->addPass($pass);
$mirrorHeaders->addPass($passOne);
$remove = 'remove-one';
$removeOne = 'test-two';
$mirrorHeaders->addRemove($remove);
$mirrorHeaders->addRemove($removeOne);

$set = new WebsiteMirrorHeadersSet();
$set->setKey("key1");
$set->setValue("val1");

$mirrorHeaders->addSet($set);

$setOne = new WebsiteMirrorHeadersSet();
$setOne->setKey("key2");
$setOne->setValue("val2");

$mirrorHeaders->addSet($setOne);

$websiteRedirect->setMirrorHeaders($mirrorHeaders);

$routingRule->setRedirect($websiteRedirect);
$routingRule->setCondition($websiteCondition);

$websiteConfig->addRule($routingRule);

$routingRuleOne = new WebsiteRoutingRule();

$routingRuleOne->setNumber(2);

$websiteCondition = new WebsiteCondition();

$includeHeader = new WebsiteIncludeHeader();
$includeHeader->setKey('host');
$includeHeader->setEquals('test.oss-cn-beijing-internal.aliyuncs.com');
$websiteCondition->addIncludeHeader($includeHeader);

$includeHeaderOne = new WebsiteIncludeHeader();
$includeHeaderOne->setKey('host_two');
$includeHeaderOne->setEquals('demo.oss-cn-beijing-internal.aliyuncs.com');
$websiteCondition->addIncludeHeader($includeHeaderOne);
$websiteCondition->setKeyPrefixEquals('abc/');
$websiteCondition->setHttpErrorCodeReturnedEquals(404);
$routingRuleOne->setCondition($websiteCondition);

$websiteRedirect = new WebsiteRedirect();
$websiteRedirect->setRedirectType(WebsiteRedirect::ALICDN);
$websiteRedirect->setProtocol(WebsiteRedirect::HTTP);
$websiteRedirect->setPassQueryString(false);
$websiteRedirect->setReplaceKeyWith('prefix/${key}.jpg');
$websiteRedirect->setEnableReplacePrefix(false);
$websiteRedirect->setHttpRedirectCode(301);
$routingRuleOne->setRedirect($websiteRedirect);
$websiteConfig->addRule($routingRuleOne);

$routingRuleTwo = new WebsiteRoutingRule();
$routingRuleTwo->setNumber(3);
$websiteCondition = new WebsiteCondition();
$websiteCondition->setKeyPrefixEquals("abc/");
$websiteCondition->setHttpErrorCodeReturnedEquals(404);
$routingRuleTwo->setCondition($websiteCondition);

$websiteRedirect = new WebsiteRedirect();
$websiteRedirect->setRedirectType(WebsiteRedirect::EXTERNAL);
$websiteRedirect->setProtocol(WebsiteRedirect::HTTPS);
$websiteRedirect->setHostName("demo.com");
$websiteRedirect->setPassQueryString(false);
$websiteRedirect->setReplaceKeyWith('prefix/${key}');
$websiteRedirect->setEnableReplacePrefix(false);
$websiteRedirect->setHttpRedirectCode(302);

$routingRuleTwo->setRedirect($websiteRedirect);
$websiteConfig->addRule($routingRuleTwo);
$ossClient->putBucketWebsite($bucket,$websiteConfig);
Common::println("bucket $bucket websiteConfig created:" . $websiteConfig->serializeToXml());

// Get Mirror back to source configuration
$result = $ossClient->getBucketWebsite($bucket);

if ($result->getIndexDocument() !== null){
    if ($result->getIndexDocument()->getSuffix()){
        Common::println("Index Document:" . $result->getIndexDocument()->getSuffix().PHP_EOL);
    }
    if ($result->getIndexDocument()->getSupportSubDir()){
        Common::println("Index Document Support Sub Dir:" . $result->getIndexDocument()->getSupportSubDir().PHP_EOL);
    }
    if ($result->getIndexDocument()->getType()){
        Common::println("Index Document Type:" . $result->getIndexDocument()->getType().PHP_EOL);
    }
}

if ($result->getErrorDocument() !== null){
    if ($result->getErrorDocument()->getKey()){
        Common::println("Error Document Key:" . $result->getErrorDocument()->getKey().PHP_EOL);
    }
    if ($result->getErrorDocument()->getHttpStatus()){
        Common::println("Error Document Http Status:" . $result->getErrorDocument()->getHttpStatus().PHP_EOL);
    }

}


if($result->getRoutingRules() !== null){
    foreach ($result->getRoutingRules() as $rule){
        Common::println("Routing Rule Number:" . $rule->getRuleNumber().PHP_EOL);
        if($rule->getCondition()){
            if($rule->getCondition()->getKeyPrefixEquals()){
                Common::println("Routing Rule Condition Key Prefix Equals:" . $rule->getCondition()->getKeyPrefixEquals().PHP_EOL);
            }
            if($rule->getCondition()->getKeySuffixEquals()){
                Common::println("Routing Rule Condition Key Suffix Equals:" . $rule->getCondition()->getKeySuffixEquals().PHP_EOL);
            }
            if($rule->getCondition()->getHttpErrorCodeReturnedEquals()){
                Common::println("Routing Rule Condition Http Error Code Returned Equals:" . $rule->getCondition()->getHttpErrorCodeReturnedEquals().PHP_EOL);
            }
            if($rule->getCondition()->getIncludeHeader()){
                foreach ($rule->getCondition()->getIncludeHeader() as $headers){
                    Common::println("Routing Rule Condition Include Headers Key:" . $headers->getKey().PHP_EOL);
                    Common::println("Routing Rule Condition Include Headers Equals:" . $headers->getEquals().PHP_EOL);
                }
            }
        }
        if($rule->getRedirect()){
            if($rule->getRedirect()->getRedirectType()){
                Common::println("Routing Rule Redirect Redirect Type:" . $rule->getRedirect()->getRedirectType().PHP_EOL);
            }
            if($rule->getRedirect()->getPassQueryString()){
                Common::println("Routing Rule Redirect Pass Query String:" . $rule->getRedirect()->getPassQueryString().PHP_EOL);
            }
            if($rule->getRedirect()->getProtocol()){
                Common::println("Routing Rule Redirect Protocol:" . $rule->getRedirect()->getProtocol().PHP_EOL);
            }
            if($rule->getRedirect()->getHostName()){
                Common::println("Routing Rule Redirect Host Name:" . $rule->getRedirect()->getHostName().PHP_EOL);
            }
            if($rule->getRedirect()->getReplaceKeyWith()){
                Common::println("Routing Rule Redirect Replace Key:" . $rule->getRedirect()->getReplaceKeyWith().PHP_EOL);
            }
            if($rule->getRedirect()->getEnableReplacePrefix()){
                Common::println("Routing Rule Redirect Replace Prefix:" . $rule->getRedirect()->getEnableReplacePrefix().PHP_EOL);
            }
            if($rule->getRedirect()->getReplaceKeyPrefixWith()){
                Common::println("Routing Rule Redirect Replace Key Prefix With:" . $rule->getRedirect()->getReplaceKeyPrefixWith().PHP_EOL);
            }
            if($rule->getRedirect()->getMirrorURL()){
                Common::println("Routing Rule Redirect Mirror Url:" . $rule->getRedirect()->getMirrorURL().PHP_EOL);
            }
            if($rule->getRedirect()->getHttpRedirectCode()){
                Common::println("Routing Rule Redirect Http Redirect Code:" . $rule->getRedirect()->getHttpRedirectCode().PHP_EOL);
            }
            if($rule->getRedirect()->getMirrorPassQueryString()){
                Common::println("Routing Rule Redirect Mirror Pass Query String:" . $rule->getRedirect()->getMirrorPassQueryString().PHP_EOL);
            }
            if($rule->getRedirect()->getMirrorFollowRedirect()){
                Common::println("Routing Rule Redirect Mirror Follow Redirect:" . $rule->getRedirect()->getMirrorFollowRedirect().PHP_EOL);
            }
            if($rule->getRedirect()->getMirrorCheckMd5()){
                Common::println("Routing Rule Redirect Mirror Check Md5:" . $rule->getRedirect()->getMirrorCheckMd5().PHP_EOL);
            }
            if($rule->getRedirect()->getMirrorHeaders()){
                $headerObject = $rule->getRedirect()->getMirrorHeaders();
                Common::println("Routing Rule Redirect Mirror Headers Pass All:" . $headerObject->getPassAll().PHP_EOL);
                if($headerObject->getPass()){
                    foreach ($headerObject->getPass() as $pass){
                        Common::println("Routing Rule Redirect Mirror Headers Pass:" . $pass.PHP_EOL);
                    }
                }
                if($headerObject->getRemove()){
                    foreach ($headerObject->getRemove() as $remove){
                        Common::println("Routing Rule Redirect Mirror Headers Remove:" . $remove.PHP_EOL);
                    }
                }
                if($headerObject->getSet()){
                    foreach ($headerObject->getSet() as $set){
                        Common::println("Routing Rule Redirect Mirror Headers Set Key:" . $set->getKey().PHP_EOL);
                        Common::println("Routing Rule Redirect Mirror Headers Set Value:" . $set->getValue().PHP_EOL);
                    }
                }
            }

        }
    }
}


//******************************* For complete usage, see the following functions  ****************************************************
putBucketWebsite($ossClient, $bucket);
getBucketWebsite($ossClient, $bucket);
deleteBucketWebsite($ossClient, $bucket);
getBucketWebsite($ossClient, $bucket);
putBucketWebsiteMirror($ossClient, $bucket);
getBucketWebsiteMirror($ossClient, $bucket);

/**
 * Sets bucket static website configuration
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function putBucketWebsite($ossClient, $bucket)
{
    $websiteConfig = new WebsiteConfig("index.html", "error.html");
    try {
        $ossClient->putBucketWebsite($bucket, $websiteConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket static website configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception|OssException
 */
function getBucketWebsite($ossClient, $bucket)
{
    try {
        $websiteConfig = $ossClient->getBucketWebsite($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($websiteConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket static website configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function deleteBucketWebsite($ossClient, $bucket)
{
    try {
        $ossClient->deleteBucketWebsite($bucket);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Sets bucket static website configuration mirror
 * @param $ossClient OssClient
 * @param $bucket string bucket name
 * @throws OssException|RequestCore_Exception
 */
function putBucketWebsiteMirror($ossClient, $bucket)
{
    try {
        $index = new WebsiteIndexDocument("index.html");
        $error = new WebsiteErrorDocument("error.html");
        $websiteConfig = new WebsiteConfig($index, $error);

        $index->setSupportSubDir(false);
        $index->setType(0);
        $error->setHttpStatus(404);

        $routingRule = new WebsiteRoutingRule();
        $routingRule->setNumber(1);
        $websiteCondition = new WebsiteCondition();
        $websiteCondition->setKeyPrefixEquals("examplebucket");
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);

        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::MIRROR);
        $websiteRedirect->setMirrorURL('https://www.example.com/');
        $websiteRedirect->setPassQueryString(true);
        $websiteRedirect->setMirrorPassQueryString(true);
        // Specifies the status code returned when jumping
        //$websiteRedirect->setHttpRedirectCode(302);
        // Specifies the domain name at jump time
//        $websiteRedirect->setHostName("oss.aliyuncs.com");
        // Specifies the protocol at jump time. It takes effect only when redirecttype is set to external or alicdn.
        //$websiteRedirect->setProtocol(WebsiteRedirect::HTTPS);
        // During redirect, the object name will be replaced with the value specified by replacekeywith. Replacekeywith supports setting variables.
        $websiteRedirect->setReplaceKeyWith('${key}.jpg');
        // If this field is set to true, the prefix of the object will be replaced with the value specified by replacekeyprefixwith.
        $websiteRedirect->setEnableReplacePrefix(false);
        // When redirect, the prefix of the object name will be replaced with this value.
//        $websiteRedirect->setReplaceKeyPrefixWith('examplebucket');
        // Check MD5 of the source body
        $websiteRedirect->setMirrorCheckMd5(true);

        $mirrorHeaders = new WebsiteMirrorHeaders();
        // Whether to transparently transmit headers other than the following headers to the source station

        $mirrorHeaders->setPassAll(true);
        $pass = 'cache-control-one';
        $passOne = 'pass-one';
        $mirrorHeaders->addPass($pass);
        $mirrorHeaders->addPass($passOne);
        $remove = 'remove-one';
        $removeOne = 'test-two';
        $mirrorHeaders->addRemove($remove);
        $mirrorHeaders->addRemove($removeOne);

        $set = new WebsiteMirrorHeadersSet();
        $set->setKey("key1");
        $set->setValue("val1");
        $mirrorHeaders->addSet($set);

        $setOne = new WebsiteMirrorHeadersSet();
        $setOne->setKey("key2");
        $setOne->setValue("val2");
        $mirrorHeaders->addSet($setOne);

        $websiteRedirect->setMirrorHeaders($mirrorHeaders);
        $routingRule->setRedirect($websiteRedirect);
        $routingRule->setCondition($websiteCondition);

        $websiteConfig->addRule($routingRule);
        $ossClient->putBucketWebsite($bucket, $websiteConfig);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    printf("bucket $bucket websiteConfig created:" . $websiteConfig->serializeToXml() . "\n");
}


/**
 * Get bucket static website configuration mirror
 * @param $ossClient OssClient
 * @param $bucket string bucket name
 * @throws RequestCore_Exception
 */
function getBucketWebsiteMirror($ossClient, $bucket)
{
    try {
        $result = $ossClient->getBucketWebsite($bucket);
        if ($result->getIndexDocument() !== null){
            if ($result->getIndexDocument()->getSuffix()){
                Common::println("Index Document:" . $result->getIndexDocument()->getSuffix().PHP_EOL);
            }
            if ($result->getIndexDocument()->getSupportSubDir()){
                Common::println("Index Document Support Sub Dir:" . $result->getIndexDocument()->getSupportSubDir().PHP_EOL);
            }
            if ($result->getIndexDocument()->getType()){
                Common::println("Index Document Type:" . $result->getIndexDocument()->getType().PHP_EOL);
            }
        }

        if ($result->getErrorDocument() !== null){
            if ($result->getErrorDocument()->getKey()){
                Common::println("Error Document Key:" . $result->getErrorDocument()->getKey().PHP_EOL);
            }
            if ($result->getErrorDocument()->getHttpStatus()){
                Common::println("Error Document Http Status:" . $result->getErrorDocument()->getHttpStatus().PHP_EOL);
            }

        }

        if($result->getRoutingRules() !== null){
            foreach ($result->getRoutingRules() as $rule){
                Common::println("Routing Rule Number:" . $rule->getRuleNumber().PHP_EOL);
                if($rule->getCondition()){
                    if($rule->getCondition()->getKeyPrefixEquals()){
                        Common::println("Routing Rule Condition Key Prefix Equals:" . $rule->getCondition()->getKeyPrefixEquals().PHP_EOL);
                    }
                    if($rule->getCondition()->getKeySuffixEquals()){
                        Common::println("Routing Rule Condition Key Suffix Equals:" . $rule->getCondition()->getKeySuffixEquals().PHP_EOL);
                    }
                    if($rule->getCondition()->getHttpErrorCodeReturnedEquals()){
                        Common::println("Routing Rule Condition Http Error Code Returned Equals:" . $rule->getCondition()->getHttpErrorCodeReturnedEquals().PHP_EOL);
                    }
                    if($rule->getCondition()->getIncludeHeader()){
                        foreach ($rule->getCondition()->getIncludeHeader() as $headers){
                            Common::println("Routing Rule Condition Include Headers Key:" . $headers->getKey().PHP_EOL);
                            Common::println("Routing Rule Condition Include Headers Equals:" . $headers->getEquals().PHP_EOL);
                        }
                    }
                }
                if($rule->getRedirect()){
                    if($rule->getRedirect()->getRedirectType()){
                        Common::println("Routing Rule Redirect Redirect Type:" . $rule->getRedirect()->getRedirectType().PHP_EOL);
                    }
                    if($rule->getRedirect()->getPassQueryString()){
                        Common::println("Routing Rule Redirect Pass Query String:" . $rule->getRedirect()->getPassQueryString().PHP_EOL);
                    }
                    if($rule->getRedirect()->getProtocol()){
                        Common::println("Routing Rule Redirect Protocol:" . $rule->getRedirect()->getProtocol().PHP_EOL);
                    }
                    if($rule->getRedirect()->getHostName()){
                        Common::println("Routing Rule Redirect Host Name:" . $rule->getRedirect()->getHostName().PHP_EOL);
                    }
                    if($rule->getRedirect()->getReplaceKeyWith()){
                        Common::println("Routing Rule Redirect Replace Key:" . $rule->getRedirect()->getReplaceKeyWith().PHP_EOL);
                    }
                    if($rule->getRedirect()->getEnableReplacePrefix()){
                        Common::println("Routing Rule Redirect Replace Prefix:" . $rule->getRedirect()->getEnableReplacePrefix().PHP_EOL);
                    }
                    if($rule->getRedirect()->getReplaceKeyPrefixWith()){
                        Common::println("Routing Rule Redirect Replace Key Prefix With:" . $rule->getRedirect()->getReplaceKeyPrefixWith().PHP_EOL);
                    }
                    if($rule->getRedirect()->getMirrorURL()){
                        Common::println("Routing Rule Redirect Mirror Url:" . $rule->getRedirect()->getMirrorURL().PHP_EOL);
                    }
                    if($rule->getRedirect()->getHttpRedirectCode()){
                        Common::println("Routing Rule Redirect Http Redirect Code:" . $rule->getRedirect()->getHttpRedirectCode().PHP_EOL);
                    }
                    if($rule->getRedirect()->getMirrorPassQueryString()){
                        Common::println("Routing Rule Redirect Mirror Pass Query String:" . $rule->getRedirect()->getMirrorPassQueryString().PHP_EOL);
                    }
                    if($rule->getRedirect()->getMirrorFollowRedirect()){
                        Common::println("Routing Rule Redirect Mirror Follow Redirect:" . $rule->getRedirect()->getMirrorFollowRedirect().PHP_EOL);
                    }
                    if($rule->getRedirect()->getMirrorCheckMd5()){
                        Common::println("Routing Rule Redirect Mirror Check Md5:" . $rule->getRedirect()->getMirrorCheckMd5().PHP_EOL);
                    }
                    if($rule->getRedirect()->getMirrorHeaders()){
                        $headerObject = $rule->getRedirect()->getMirrorHeaders();
                        Common::println("Routing Rule Redirect Mirror Headers Pass All:" . $headerObject->getPassAll().PHP_EOL);
                        if($headerObject->getPass()){
                            foreach ($headerObject->getPass() as $pass){
                                Common::println("Routing Rule Redirect Mirror Headers Pass:" . $pass.PHP_EOL);
                            }
                        }
                        if($headerObject->getRemove()){
                            foreach ($headerObject->getRemove() as $remove){
                                Common::println("Routing Rule Redirect Mirror Headers Remove:" . $remove.PHP_EOL);
                            }
                        }
                        if($headerObject->getSet()){
                            foreach ($headerObject->getSet() as $set){
                                Common::println("Routing Rule Redirect Mirror Headers Set Key:" . $set->getKey().PHP_EOL);
                                Common::println("Routing Rule Redirect Mirror Headers Set Value:" . $set->getValue().PHP_EOL);
                            }
                        }
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
