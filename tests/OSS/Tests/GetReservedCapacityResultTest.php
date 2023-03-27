<?php

namespace OSS\Tests;


use OSS\Http\ResponseCore;
use OSS\Result\GetReservedCapacityResult;

class CreateReservedCapacityTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
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
<AutoExpansionSize>100</AutoExpansionSize>
<AutoExpansionMaxSize>20480</AutoExpansionMaxSize>
<CreateTime>1676106931</CreateTime>
<LastModifyTime>1676224171</LastModifyTime>
<EnableTime>0</EnableTime>
</ReservedCapacityRecord>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityRecord>
</ReservedCapacityRecord>
BBBB;


    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetReservedCapacityResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $record = $result->getData();

        $this->assertEquals($record->getOwnerId(),"ut_test_put_bucket");
        $this->assertEquals($record->getOwnerDisplayName(),"ut_test_put_bucket");
        $this->assertEquals($record->getInstanceId(),"dd67179e-77b7-415f-a571-e3396e926356");
        $this->assertEquals($record->getName(),"test-rc1");
        $this->assertEquals($record->getRegion(),"oss-cn-hangzhou");
        $this->assertEquals($record->getStatus(),"Enabled");
        $this->assertEquals($record->getDataRedundancyType(),"LRS");
        $this->assertEquals($record->getReservedCapacity(),20480);
        $this->assertEquals($record->getAutoExpansionSize(),100);
        $this->assertEquals($record->getAutoExpansionMaxSize(),20480);
        $this->assertEquals($record->getCreateTime(),1676106931);
        $this->assertEquals($record->getLastModifyTime(),1676224171);
        $this->assertEquals($record->getEnableTime(),0);
    }


    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetReservedCapacityResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $record = $result->getData();

        $this->assertNull($record->getOwnerId());
        $this->assertNull($record->getRegion());
        $this->assertNull($record->getEnableTime());
    }
}
