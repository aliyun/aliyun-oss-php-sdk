<?php

namespace OSS\Tests;

use OSS\Model\InventoryConfig;
use OSS\Model\InventoryConfigFilter;
use OSS\Model\InventoryConfigOptionalFields;
use OSS\Model\InventoryConfigOssBucketDestination;

class InventoryConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
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
<Encryption>
<SSE-KMS>
<KeyId>keyId</KeyId>
</SSE-KMS>
</Encryption>
</OSSBucketDestination>
</Destination>
<Schedule>
<Frequency>Daily</Frequency>
</Schedule>
<Filter>
<Prefix>filterPrefix/</Prefix>
<LastModifyBeginTimeStamp>1637883649</LastModifyBeginTimeStamp>
<LastModifyEndTimeStamp>1638347592</LastModifyEndTimeStamp>
<LowerSizeBound>1024</LowerSizeBound>
<UpperSizeBound>1048576</UpperSizeBound>
<StorageClass>Standard,IA</StorageClass>
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
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<InventoryConfiguration/>
BBBB;

    public function testValidXml()
    {
        $id = "report1";
        $isEnabled = InventoryConfig::IS_ENABLED_TRUE;
        $filterPrefix = "filterPrefix/";
        $version = InventoryConfig::OBJECT_VERSION_ALL;
        $frequency = InventoryConfig::FREQUENCY_DAILY;
        $kmsId = "keyId";

        $LastModifyBeginTimeStamp = "1637883649";
        $LastModifyEndTimeStamp = "1638347592";
        $LowerSizeBound = "1024";
        $UpperSizeBound = "1048576";
        $StorageClass = "Standard,IA";
        $configFilter = new InventoryConfigFilter($filterPrefix,$LastModifyBeginTimeStamp,$LastModifyEndTimeStamp,$LowerSizeBound,$UpperSizeBound,$StorageClass);
        $files = array(
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_SIZE),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_LAST_MODIFIED_DATE),

            new InventoryConfigOptionalFields(InventoryConfig::FIELD_ETAG),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_STORAGE_CLASS),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_IS_MULTIPART_UPLOADED),
            new InventoryConfigOptionalFields(InventoryConfig::FIELD_ENCRYPTION_STATUS),
        );

        $format = InventoryConfigOssBucketDestination::DEST_FORMAT;
        $accountId = '1000000000000000';
        $roleArn = 'acs:ram::1000000000000000:role/AliyunOSSRole';
        $bucketName = 'acs:oss:::destination-bucket';
        $prefix = 'prefix1';
        $configDestination = new InventoryConfigOssBucketDestination($format,$accountId,$roleArn,$bucketName,$prefix,null,$kmsId);


        $inventoryConfig = new InventoryConfig($id,$isEnabled,$frequency,$version,$configDestination,$configFilter,$files);
        $this->assertEquals($this->cleanXml($inventoryConfig->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXml()
    {
        $inventoryConfig = new InventoryConfig();
        $inventoryConfig->parseFromXml($this->cleanXml($this->invalidXml));
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml($inventoryConfig->serializeToXml()));
    }

    public function testInvalidXmlOne()
    {
        $inventoryConfig = new InventoryConfig();
        $inventoryConfig->parseFromXml($this->cleanXml($this->validXml));
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($inventoryConfig->serializeToXml()));
    }



    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
