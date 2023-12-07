<?php


namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetLiveChannelHistoryResult;

class GetLiveChannelHistoryResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<LiveChannelHistory>
  <LiveRecord>
    <StartTime>2016-07-30T01:53:21.000Z</StartTime>
    <EndTime>2016-07-30T01:53:31.000Z</EndTime>
    <RemoteAddr>10.101.194.148:56861</RemoteAddr>
  </LiveRecord>
  <LiveRecord>
    <StartTime>2016-07-30T01:53:35.000Z</StartTime>
    <EndTime>2016-07-30T01:53:45.000Z</EndTime>
    <RemoteAddr>10.101.194.148:57126</RemoteAddr>
  </LiveRecord>
  <LiveRecord>
    <StartTime>2016-07-30T01:53:49.000Z</StartTime>
    <EndTime>2016-07-30T01:53:59.000Z</EndTime>
    <RemoteAddr>10.101.194.148:57577</RemoteAddr>
  </LiveRecord>
  <LiveRecord>
    <StartTime>2016-07-30T01:54:04.000Z</StartTime>
    <EndTime>2016-07-30T01:54:14.000Z</EndTime>
    <RemoteAddr>10.101.194.148:57632</RemoteAddr>
  </LiveRecord>
</LiveChannelHistory>
BBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetLiveChannelHistoryResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $history = $result->getData();
        $recordList = $history->getLiveRecordList();
        $this->assertEquals("2016-07-30T01:53:21.000Z", $recordList[0]->getStartTime());
        $this->assertEquals("2016-07-30T01:53:45.000Z", $recordList[1]->getEndTime());
        $this->assertEquals("10.101.194.148:57577", $recordList[2]->getRemoteAddr());

    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), "", 200);
        $result = new GetLiveChannelHistoryResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $history = $result->getData();
        $this->assertEquals(null,$history->getLiveRecordList);
    }
}

