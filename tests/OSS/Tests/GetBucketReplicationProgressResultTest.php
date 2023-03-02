<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetBucketReplicationProgressResult;

class GetBucketReplicationProgressResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" ?>
<ReplicationProgress>
 <Rule>
   <ID>test_replication_1</ID>
   <PrefixSet>
    <Prefix>source_image</Prefix>
    <Prefix>video</Prefix>
   </PrefixSet>
   <Action>PUT</Action>
   <Destination>
    <Bucket>target-bucket</Bucket>
    <Location>oss-cn-beijing</Location>
    <TransferType>oss_acc</TransferType>
   </Destination>
   <Status>doing</Status>
   <HistoricalObjectReplication>enabled</HistoricalObjectReplication>
   <Progress>
    <HistoricalObject>0.85</HistoricalObject>
    <NewObject>2015-09-24T15:28:14.000Z</NewObject>
   </Progress>
 </Rule>
</ReplicationProgress>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationProgress></ReplicationProgress>
BBBB;

    public function testValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketReplicationProgressResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $rs = $result->getData();
        $info = $rs->getRule();

        $this->assertEquals("test_replication_1", $info->getId());


        if ($info->getPrefixSet()) {
            $this->assertEquals("source_image", $info->getPrefixSet()[0]);
            $this->assertEquals("video", $info->getPrefixSet()[1]);
        }

        $this->assertEquals("PUT", $info->getAction());
        $this->assertEquals("doing", $info->getStatus());
        $this->assertEquals("enabled", $info->getHistoricalObjectReplication());

        $destination = $info->getDestination();
        $this->assertEquals("target-bucket", $destination->getBucket());
        $this->assertEquals("oss-cn-beijing", $destination->getLocation());
        $this->assertEquals("oss_acc", $destination->getTransferType());


        $progress = $info->getProgress();
        $this->assertEquals("0.85", $progress->getHistoricalObject());
        $this->assertEquals("2015-09-24T15:28:14.000Z", $progress->getNewObject());
    }

    public function testInvalidXml()
    {
        $response = new ResponseCore(array(), $this->invalidXml, 200);
        $result = new GetBucketReplicationProgressResult($response);
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
