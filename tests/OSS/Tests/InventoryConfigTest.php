<?php

namespace OSS\Tests;

use OSS\Model\InventoryConfig;

class InventoryConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<InventoryConfiguration>
<Id>report1</Id>
<IsEnabled>true</IsEnabled>
<Filter>
<Prefix>filterPrefix/</Prefix>
</Filter>
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
<InventoryConfiguration></InventoryConfiguration>
BBBB;

    public function testValidXmlXml()
    {
        $inventoryConfig = new InventoryConfig();
        $inventoryConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($inventoryConfig->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXml()
    {
        $inventoryConfig = new InventoryConfig();
        $inventoryConfig->parseFromXml($this->cleanXml($this->invalidXml));
        $this->assertEquals(array(), $inventoryConfig->getConfigs());
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
