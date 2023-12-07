<?php

namespace OSS\Tests;

use OSS\Model\ListInventoryConfig;

class ListBucketInventoryResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
  <ListInventoryConfigurationsResult>
     <InventoryConfiguration>
        <Id>report1</Id>
        <IsEnabled>true</IsEnabled>
        <Destination>
           <OSSBucketDestination>
              <Format>CSV</Format>
              <AccountId>1000000000000000</AccountId>
              <RoleArn>acs:ram::1000000000000000:role/AliyunOSSRole</RoleArn>
              <Bucket>acs:oss:::destination-bucket</Bucket>
              <Prefix>prefix1</Prefix>
           </OSSBucketDestination>
        </Destination>
        <Schedule>
           <Frequency>Daily</Frequency>
        </Schedule>
        <Filter>
           <Prefix>prefix/One</Prefix>
        </Filter>
        <IncludedObjectVersions>All</IncludedObjectVersions>
        <OptionalFields>
           <Field>Size</Field>
           <Field>LastModifiedDate</Field>
           <Field>ETag</Field>
           <Field>StorageClass</Field>
           <Field>IsMultipartUploaded</Field>
           <Field>EncryptionStatus</Field>
        </OptionalFields>
     </InventoryConfiguration>
     <InventoryConfiguration>
        <Id>report2</Id>
        <IsEnabled>true</IsEnabled>
        <Destination>
           <OSSBucketDestination>
              <Format>CSV</Format>
              <AccountId>1000000000000000</AccountId>
              <RoleArn>acs:ram::1000000000000000:role/AliyunOSSRole</RoleArn>
              <Bucket>acs:oss:::destination-bucket</Bucket>
              <Prefix>prefix2</Prefix>
              <Encryption>
                <SSE-OSS><KeyId>oss-key-id</KeyId></SSE-OSS>
            </Encryption>
           </OSSBucketDestination>
        </Destination>
        <Schedule>
           <Frequency>Daily</Frequency>
        </Schedule>
        <Filter>
           <Prefix>prefix/Two</Prefix>
        </Filter>
        <IncludedObjectVersions>All</IncludedObjectVersions>
        <OptionalFields>
           <Field>Size</Field>
           <Field>LastModifiedDate</Field>
           <Field>ETag</Field>
           <Field>StorageClass</Field>
           <Field>IsMultipartUploaded</Field>
           <Field>EncryptionStatus</Field>
        </OptionalFields>
     </InventoryConfiguration>
     <InventoryConfiguration>
        <Id>report3</Id>
        <IsEnabled>true</IsEnabled>
        <Destination>
           <OSSBucketDestination>
              <Format>CSV</Format>
              <AccountId>1000000000000000</AccountId>
              <RoleArn>acs:ram::1000000000000000:role/AliyunOSSRole</RoleArn>
              <Bucket>acs:oss:::destination-bucket</Bucket>
              <Prefix>prefix3</Prefix>
              <Encryption>
                <SSE-KMS><KeyId>kms-key-id</KeyId></SSE-KMS>
            </Encryption>
           </OSSBucketDestination>
        </Destination>
        <Schedule>
           <Frequency>Daily</Frequency>
        </Schedule>
        <Filter>
           <Prefix>prefix/Three</Prefix>
        </Filter>
        <IncludedObjectVersions>All</IncludedObjectVersions>
        <OptionalFields>
           <Field>Size</Field>
           <Field>LastModifiedDate</Field>
           <Field>ETag</Field>
           <Field>StorageClass</Field>
           <Field>IsMultipartUploaded</Field>
           <Field>EncryptionStatus</Field>
        </OptionalFields>
     </InventoryConfiguration>
     <IsTruncated>true</IsTruncated>
     <NextContinuationToken>11111</NextContinuationToken> 
  </ListInventoryConfigurationsResult>
BBBB;

    public function testValidXml()
    {
        $config = new ListInventoryConfig();
        $config->parseFromXml($this->validXml);



        $config1 = $config->getInventoryList();

        $this->assertEquals("true",$config->getIsTruncated());
        $this->assertEquals("11111",$config->getNextContinuationToken());
        $this->assertEquals("report1",$config1[0]->getId());
        $this->assertEquals("true",$config1[0]->getIsEnabled());
        $this->assertEquals("Daily",$config1[0]->getSchedule());
        $this->assertEquals("1000000000000000",$config1[0]->getDestination()->getAccountId());
        $this->assertEquals("acs:ram::1000000000000000:role/AliyunOSSRole",$config1[0]->getDestination()->getRoleArn());
        $this->assertEquals("acs:oss:::destination-bucket",$config1[0]->getDestination()->getBucket());
        $this->assertNull($config1[0]->getDestination()->getOssId());
        $this->assertEquals("oss-key-id",$config1[1]->getDestination()->getOssId());

        $this->assertEquals("report2",$config1[1]->getId());
        $this->assertEquals("true",$config1[1]->getIsEnabled());
        $this->assertEquals("Daily",$config1[1]->getSchedule());
        $this->assertEquals("1000000000000000",$config1[1]->getDestination()->getAccountId());
        $this->assertEquals("acs:ram::1000000000000000:role/AliyunOSSRole",$config1[1]->getDestination()->getRoleArn());
        $this->assertEquals("acs:oss:::destination-bucket",$config1[1]->getDestination()->getBucket());

        $this->assertEquals("oss-key-id",$config1[1]->getDestination()->getOssId());
        $this->assertEquals("kms-key-id",$config1[2]->getDestination()->getKmsId());
    }
}
