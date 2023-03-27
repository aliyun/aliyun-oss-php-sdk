<?php

namespace OSS\Tests;


use OSS\Http\ResponseCore;
use OSS\Result\ListBucketWithReservedCapacityResult;

class ListBucketWithReservedCapacityResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<ReservedCapacityBucketList>
  <InstanceId>dd67179e-77b7-415f-a571-e3396e926356</InstanceId>
  <BucketList>
    <Bucket>test-rc</Bucket>
    <Bucket>mxx-test-rc</Bucket>
  </BucketList>
</ReservedCapacityBucketList>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityBucketList>
<InstanceId>dd67179e-77b7-415f-a571-e3396e926356</InstanceId>
</ReservedCapacityBucketList>
BBBB;


    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListBucketWithReservedCapacityResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $list = $result->getData();

        $this->assertEquals($list->getInstanceId(),"dd67179e-77b7-415f-a571-e3396e926356");

        $bucketList = $list->getBucketList();
        $this->assertEquals($bucketList[0],"test-rc");
        $this->assertEquals($bucketList[1],"mxx-test-rc");
    }


    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new ListBucketWithReservedCapacityResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $list = $result->getData();

        $this->assertEquals($list->getInstanceId(),"dd67179e-77b7-415f-a571-e3396e926356");

        $bucketList = $list->getBucketList();
        $this->assertNull($bucketList);
    }
}
