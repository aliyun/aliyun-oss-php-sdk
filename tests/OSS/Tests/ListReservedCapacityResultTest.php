<?php

namespace OSS\Tests;


use OSS\Http\ResponseCore;
use OSS\Result\ListReservedCapacityResult;

class ListReservedCapacityResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityRecordList>
  <ReservedCapacityRecord>
    <InstanceId>e72beabd-33ed-4c21-8069-0b6cf8a0dfc2</InstanceId>
    <Name>test-rc</Name>
    <Owner>
      <ID>ut_test_put_bucket</ID>
      <DisplayName>ut_test_put_bucket</DisplayName>
    </Owner>
    <Region>oss-cn-hangzhou</Region>
    <Status>Init</Status>
    <DataRedundancyType>ZRS</DataRedundancyType>
    <ReservedCapacity>20480</ReservedCapacity>
    <CreateTime>1676106871</CreateTime>
    <LastModifyTime>1676106871</LastModifyTime>
    <EnableTime>0</EnableTime>
  </ReservedCapacityRecord>
  <ReservedCapacityRecord>
    <InstanceId>dd67179e-77b7-415f-a571-e3396e926356</InstanceId>
    <Name>test-rc1</Name>
    <Owner>
      <ID>ut_test_put_bucket</ID>
      <DisplayName>ut_test_put_bucket</DisplayName>
    </Owner>
    <Region>oss-cn-hangzhou</Region>
    <Status>Enabled</Status>
    <DataRedundancyType>LRS</DataRedundancyType>
    <ReservedCapacity>20480</ReservedCapacity>
    <CreateTime>1676106931</CreateTime>
    <LastModifyTime>1676224171</LastModifyTime>
    <EnableTime>0</EnableTime>
  </ReservedCapacityRecord>
  <ReservedCapacityRecord>
    <InstanceId>db682d06-215a-4079-8022-31d1e0746b36</InstanceId>
    <Name>testrc1</Name>
    <Owner>
      <ID>ut_test_put_bucket</ID>
      <DisplayName>ut_test_put_bucket</DisplayName>
    </Owner>
    <Region>oss-cn-hangzhou</Region>
    <Status>Init</Status>
    <DataRedundancyType>LRS</DataRedundancyType>
    <ReservedCapacity>10240</ReservedCapacity>
    <CreateTime>1676221694</CreateTime>
    <LastModifyTime>1676221694</LastModifyTime>
    <EnableTime>0</EnableTime>
  </ReservedCapacityRecord>
</ReservedCapacityRecordList>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityRecordList>
</ReservedCapacityRecordList>
BBBB;


    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListReservedCapacityResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $result1 = $result->getData();

        $list = $result1->getReservedCapacityList();
        $record = $list[0];
        $this->assertEquals($record->getOwnerId(),"ut_test_put_bucket");
        $this->assertEquals($record->getOwnerDisplayName(),"ut_test_put_bucket");
        $this->assertEquals($record->getInstanceId(),"e72beabd-33ed-4c21-8069-0b6cf8a0dfc2");
        $this->assertEquals($record->getName(),"test-rc");
        $this->assertEquals($record->getRegion(),"oss-cn-hangzhou");
        $this->assertEquals($record->getStatus(),"Init");
        $this->assertEquals($record->getDataRedundancyType(),"ZRS");
        $this->assertEquals($record->getCreateTime(),1676106871);
        $this->assertEquals($record->getLastModifyTime(),1676106871);
        $this->assertEquals($record->getEnableTime(),0);

        $record1 = $list[1];
        $this->assertEquals($record1->getOwnerId(),"ut_test_put_bucket");
        $this->assertEquals($record1->getOwnerDisplayName(),"ut_test_put_bucket");
        $this->assertEquals($record1->getInstanceId(),"dd67179e-77b7-415f-a571-e3396e926356");
        $this->assertEquals($record1->getName(),"test-rc1");
        $this->assertEquals($record1->getRegion(),"oss-cn-hangzhou");
        $this->assertEquals($record1->getStatus(),"Enabled");
        $this->assertEquals($record1->getDataRedundancyType(),"LRS");
        $this->assertEquals($record->getReservedCapacity(),20480);
        $this->assertNull($record->getAutoExpansionSize());
        $this->assertNull($record->getAutoExpansionMaxSize());
        $this->assertEquals($record1->getCreateTime(),1676106931);
        $this->assertEquals($record1->getLastModifyTime(),1676224171);
        $this->assertEquals($record1->getEnableTime(),0);
    }


    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new ListReservedCapacityResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $result1 = $result->getData();

        $this->assertNull($result1->getReservedCapacityList());
    }
}
