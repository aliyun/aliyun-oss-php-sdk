<?php

namespace OSS\Tests;

use OSS\Model\InventoryConfig;
use OSS\Result\GetBucketInventoryResult;
use OSS\Http\ResponseCore;
class GetBucketInventoryRestltTest extends \PHPUnit\Framework\TestCase
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
<Bucket>acs:oss:::bucket_0001</Bucket>
<Prefix>prefix1</Prefix>
<Encryption>
<SSE-OSS/>
</Encryption>
</OSSBucketDestination>
</Destination>
<Schedule>
<Frequency>Daily</Frequency>
</Schedule>
<Filter>
<Prefix>myprefix/</Prefix>
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
<InventoryConfiguration></InventoryConfiguration>
BBBB;

    public function testValidXmlXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketInventoryResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $this->assertEquals('report1',$result->getData()['Id']);
        $config = new InventoryConfig();
        $config->setConfigs($result->getData());
        $this->assertEquals($this->cleanXml($this->validXml),$this->cleanXml($config->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
