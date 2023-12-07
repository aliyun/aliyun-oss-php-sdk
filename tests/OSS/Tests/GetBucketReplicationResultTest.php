<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Model\BucketReplicationInfo;
use OSS\Result\GetBucketReplicationResult;
use OSS\Result\Result;

class GetBucketReplicationResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" ?>
<ReplicationConfiguration>
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
    <SyncRole>aliyunramrole</SyncRole>
  </Rule>
  <Rule>
    <ID>test_replication_2</ID>
    <PrefixSet>
      <Prefix>source_image</Prefix>
      <Prefix>video</Prefix>
    </PrefixSet>
    <Action>PUT</Action>
    <Destination>
      <Bucket>target-bucket-2</Bucket>
      <Location>oss-cn-shanghai</Location>
      <TransferType>oss_acc</TransferType>
    </Destination>
    <Status>doing</Status>
    <HistoricalObjectReplication>enabled</HistoricalObjectReplication>
    <SyncRole>aliyunramrole</SyncRole>
    <RTC><Status>enabled</Status></RTC>
  </Rule>
</ReplicationConfiguration>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationConfiguration></ReplicationConfiguration>
BBBB;

    public function testValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketReplicationResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $lists = $result->getData();
        $rule = $lists->getRules()[0];

        $this->assertEquals("test_replication_1", $rule->getId());
        $this->assertEquals("PUT", $rule->getAction());
        $this->assertEquals("doing", $rule->getStatus());
        $this->assertEquals("enabled", $rule->getHistoricalObjectReplication());
        $this->assertEquals("aliyunramrole", $rule->getSyncRole());

        $ruleOne = $lists->getRules()[1];
        $this->assertEquals("test_replication_2", $ruleOne->getId());
        $this->assertEquals("PUT", $ruleOne->getAction());
        $this->assertEquals("doing", $ruleOne->getStatus());
        $this->assertEquals("enabled", $ruleOne->getHistoricalObjectReplication());
        $this->assertEquals("aliyunramrole", $ruleOne->getSyncRole());


        if ($rule->getPrefixSet()['Prefix']) {
            $this->assertEquals("source_image", $rule->getPrefixSet()['Prefix'][0]);
            $this->assertEquals("video", $rule->getPrefixSet()['Prefix'][1]);
        }


        $destination = $rule->getDestination();

        $this->assertEquals("target-bucket", $destination->getBucket());
        $this->assertEquals("oss-cn-beijing", $destination->getLocation());
        $this->assertEquals("oss_acc", $destination->getTransferType());
        $this->assertEquals("enabled", $ruleOne->getRTC());
    }

    public function testInvalidXml()
    {
        $response = new ResponseCore(array(), $this->invalidXml, 200);
        $result = new GetBucketReplicationResult($response);
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
