<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetBucketReplicationLocationResult;

class GetBucketReplicationLocationResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" ?>
<ReplicationLocation>
  <Location>oss-cn-beijing</Location>
  <Location>oss-cn-qingdao</Location>
  <LocationTransferTypeConstraint>
    <LocationTransferType>
      <Location>oss-cn-hongkong</Location>
        <TransferTypes>
          <Type>oss_acc</Type>
        </TransferTypes>
      </LocationTransferType>
      <LocationTransferType>
        <Location>oss-us-west-1</Location>
        <TransferTypes>
          <Type>oss_acc</Type>
        </TransferTypes>
      </LocationTransferType>
    </LocationTransferTypeConstraint>
  </ReplicationLocation>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationLocation></ReplicationLocation>
BBBB;

    public function testValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketReplicationLocationResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $info = $result->getData();

        if ($info->getLocations()){
            $this->assertEquals("oss-cn-beijing", $info->getLocations()[0]);
            $this->assertEquals("oss-cn-qingdao", $info->getLocations()[1]);

        }


        if ($info->getLocationTransferTypes()){
            $this->assertEquals("oss-cn-hongkong", $info->getLocationTransferTypes()[0]['location']);
            $this->assertEquals("oss-us-west-1", $info->getLocationTransferTypes()[1]['location']);
            $this->assertEquals("oss_acc", $info->getLocationTransferTypes()[0]['type']);
            $this->assertEquals("oss_acc", $info->getLocationTransferTypes()[1]['type']);
        }

    }

    public function testInvalidXml()
    {
        $response = new ResponseCore(array(), $this->invalidXml, 200);
        $result = new GetBucketReplicationLocationResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
