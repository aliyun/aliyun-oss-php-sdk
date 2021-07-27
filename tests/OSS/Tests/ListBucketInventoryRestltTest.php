<?php

namespace OSS\Tests;

use OSS\Model\InventoryConfig;
use OSS\Result\ListBucketInventoryResult;
use OSS\Http\ResponseCore;
use OSS\Result\Result;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class ListBucketInventoryRestltTest extends \PHPUnit\Framework\TestCase
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
</ListInventoryConfigurationsResult>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<InventoryConfiguration></InventoryConfiguration>
BBBB;

    public function testValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListBucketInventoryResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
		$lists = $result->getData()->getInventoryList();
		$this->assertEquals("report1", $lists[0]->getId());
		$this->assertEquals("true", $lists[0]->getIsEnabled());
		$this->assertEquals("All", $lists[0]->getIncludedObjectVersions());
		$this->assertEquals("Daily", $lists[0]->getSchedule()['Frequency']);
		if ($lists[0]->getFilter()['Prefix']) {
			$this->assertEquals("prefix/One", $lists[0]->getFilter()['Prefix']);
		}
		if ($lists[0]->getOptionalFields()['Field']) {
			$this->assertEquals("Size", $lists[0]->getOptionalFields()['Field'][0]);
			$this->assertEquals("EncryptionStatus", $lists[0]->getOptionalFields()['Field'][5]);
		}
	
		if ($lists[0]->getOptionalFields()['Field']) {
			$this->assertEquals("Size", $lists[0]->getOptionalFields()['Field'][0]);
			$this->assertEquals("EncryptionStatus", $lists[0]->getOptionalFields()['Field'][5]);
		}
	
		$destination = $lists[0]->getOssBucketDestination();
		$this->assertEquals("CSV", $destination['Format']);
		$this->assertEquals("1000000000000000", $destination['AccountId']);
		$this->assertEquals("acs:ram::1000000000000000:role/AliyunOSSRole", $destination['RoleArn']);
		$this->assertEquals("acs:oss:::destination-bucket", $destination['Bucket']);
		$this->assertEquals("prefix1", $destination['Prefix']);
    }
	
	public function testInvalidXml()
	{
		$response = new ResponseCore(array(), $this->invalidXml, 200);
		$result = new ListBucketInventoryResult($response);
		$this->assertTrue($result->isOK());
		$this->assertNotNull($result->getData());
		$this->assertNotNull($result->getRawResponse());
		$this->assertNotNull($result->getRawResponse()->body);
		$lists = $result->getData()->getInventoryList();
		$this->assertEquals(array(),$lists);
	}

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
