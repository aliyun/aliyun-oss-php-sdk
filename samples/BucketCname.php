<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\CnameConfig;
use OSS\Model\CnameConfigCertificate;
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//*******************************Simple Usage ***************************************************************
//Create Cname Token
$myDomain = '<yourDomainName>';
$info = $ossClient->createBucketCnameToken($bucket, $myDomain);
Common::println("Bucket name: " . $info->getBucket());
Common::println("Cname is: " . $info->getCname());
Common::println("Token is: " . $info->getToken());
Common::println("ExpireTime is: " . $info->getExpireTime());

//Get Cname Token
$myDomain = '<yourDomainName>';
$info = $ossClient->getBucketCnameToken($bucket, $myDomain);
Common::println("Bucket name: " . $info->getBucket());
Common::println("Cname is: " . $info->getCname());
Common::println("Token is: " . $info->getToken());
Common::println("ExpireTime is: " . $info->getExpireTime());

// Add Cname record
$config = new CnameConfig();
$certificate = new CnameConfigCertificate();
$certificate->setCertId("927***-cn-hangzhou");
$certificate->setCertificate("-----BEGIN CERTIFICATE-----MIIGeDCCBOCgAwIBAgIRAPj4FW***-----END CERTIFICATE-----");
$certificate->setPrivateKey("-----BEGIN CERTIFICATE-----MIIFBzCCA++gAwIBAgIRALIM7***-----END CERTIFICATE-----");
$certificate->setPreviousCertId("493***-cn-hangzhou");
$certificate->setForce(true);
$certificate->setDeleteCertificate(false);
$config->setCname('<yourDomainName>');
$config->setCertificateConfig($certificate);
$ossClient->addBucketCnameV2($bucket, $config);

// Unbind Cname SSL Certificate
$config = new CnameConfig();
$certificate = new CnameConfigCertificate();
$certificate->setDeleteCertificate(true);
$config->setCname('<yourDomainName>');
$config->setCertificateConfig($certificate);
$ossClient->addBucketCnameV2($bucket, $config);


// View cname records
$cnameConfig = $ossClient->getBucketCname($bucket);
Common::println("Bucket name: " . $cnameConfig->getBucket());
Common::println("Owner is: " . $cnameConfig->getOwner());

foreach ( $cnameConfig->getCnameList() as $cnameInfo){
    Common::println("Domain is: " . $cnameInfo->getDomain());
    Common::println("Status is: " . $cnameInfo->getStatus());
    Common::println("LastModified is: " . $cnameInfo->getLastModified());
    $cert = $cnameInfo->getCertificate();
    if (isset($cert)){
        Common::println("Certificate Type is: " . $cert->getType());
        Common::println("Certificate Cert Id is: " . $cert->getCertId());
        Common::println("Certificate Status is: " . $cert->getStatus());
        Common::println("Certificate Creation Date is: " . $cert->getCreationDate());
        Common::println("Certificate Fingerprint is: " . $cert->getFingerprint());
        Common::println("Certificate Valid Start Date is: " . $cert->getValidStartDate());
        Common::println("Certificate Valid End Date is: " . $cert->getValidEndDate());
    }
}


// Delete bucket cname
$myDomain = '<yourDomainName>';
$ossClient->deleteBucketCname($bucket,$myDomain);
Common::println("bucket $bucket cname deleted");

//******************************* For complete usage, see the following functions ****************************************************

createBucketCnameToken($ossClient, $bucket);
getBucketCnameToken($ossClient, $bucket);
addBucketCname($ossClient, $bucket);
getBucketCname($ossClient, $bucket);
deleteBucketCname($ossClient, $bucket);

/**
 * Create Bucket Cname Token
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function createBucketCnameToken($ossClient, $bucket)
{
    // Set up a custom domain name.
    $myDomain = '<yourDomainName>';
    try {
        $info = $ossClient->createBucketCnameToken($bucket, $myDomain);
        printf("Bucket name: " . $info->getBucket());
        printf("Cname is: " . $info->getCname());
        printf("Token is: " . $info->getToken());
        printf("ExpireTime is: " . $info->getExpireTime());
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get Bucket Cname Token
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getBucketCnameToken($ossClient, $bucket)
{
    // Set up a custom domain name.
    $myDomain = '<yourDomainName>';
    try {
        $info = $ossClient->getBucketCnameToken($bucket, $myDomain);
        printf("Bucket name: " . $info->getBucket());
        printf("Cname is: " . $info->getCname());
        printf("Token is: " . $info->getToken());
        printf("ExpireTime is: " . $info->getExpireTime());
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Set bucket cname
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function addBucketCname($ossClient, $bucket)
{
    try {
        $config = new CnameConfig();
        $certificate = new CnameConfigCertificate();
        $certificate->setCertId("927***-cn-hangzhou");
        $certificate->setCertificate("-----BEGIN CERTIFICATE-----MIIGeDCCBOCgAwIBAgIRAPj4FW***-----END CERTIFICATE-----");
        $certificate->setPrivateKey("-----BEGIN CERTIFICATE-----MIIFBzCCA++gAwIBAgIRALIM7***-----END CERTIFICATE-----");
        $certificate->setPreviousCertId("493***-cn-hangzhou");
        $certificate->setForce(true);
        $certificate->setDeleteCertificate(false);
        $config->setCname('<yourDomainName>');
        $config->setCertificateConfig($certificate);
        $ossClient->addBucketCname($bucket, $config);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket cname
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getBucketCname($ossClient, $bucket)
{
    try {
        $cnameConfig = $ossClient->getBucketCname($bucket);
        printf("Bucket name: " . $cnameConfig->getBucket().PHP_EOL);
        printf("Owner is: " . $cnameConfig->getOwner().PHP_EOL);

        foreach ( $cnameConfig->getCnameList() as $cnameInfo){
            printf("Domain is: " . $cnameInfo->getDomain().PHP_EOL);
            printf("Status is: " . $cnameInfo->getStatus().PHP_EOL);
            printf("LastModified is: " . $cnameInfo->getLastModified().PHP_EOL);
            $cert = $cnameInfo->getCertificate();
            if (isset($cert)){
                printf("Certificate Type is: " . $cert->getType().PHP_EOL);
                printf("Certificate Cert Id is: " . $cert->getCertId().PHP_EOL);
                printf("Certificate Status is: " . $cert->getStatus().PHP_EOL);
                printf("Certificate Creation Date is: " . $cert->getCreationDate().PHP_EOL);
                printf("Certificate Fingerprint is: " . $cert->getFingerprint().PHP_EOL);
                printf("Certificate Valid Start Date is: " . $cert->getValidStartDate().PHP_EOL);
                printf("Certificate Valid End Date is: " . $cert->getValidEndDate().PHP_EOL);
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
 * Delete bucket cname
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function deleteBucketCname($ossClient, $bucket)
{
    $myDomain = '<yourDomainName>';
    try {
        $ossClient->deleteBucketCname($bucket, $myDomain);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
