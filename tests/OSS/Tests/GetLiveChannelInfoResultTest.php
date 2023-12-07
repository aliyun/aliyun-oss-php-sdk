<?php


namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetLiveChannelInfoResult;

class GetLiveChannelInfoResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<LiveChannelConfiguration>
  <Description>test</Description>
  <Status>enabled</Status>
  <Target>
    <Type>HLS</Type>
    <FragDuration>2</FragDuration>
    <FragCount>3</FragCount>
    <PlaylistName>playlist.m3u8</PlaylistName>
  </Target>
</LiveChannelConfiguration>
BBB;


    private $validXml2 = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<LiveChannelConfiguration>
  <Description>test</Description>
  <Status>enabled</Status>
  <Target>
    <Type>HLS</Type>
    <FragDuration>2</FragDuration>
    <FragCount>3</FragCount>
  </Target>
  <Snapshot>
        <RoleName>role_for_snapshot</RoleName>
        <DestBucket>snapshotdest</DestBucket>
        <NotifyTopic>snapshotnotify</NotifyTopic>
        <Interval>1</Interval>
     </Snapshot>
</LiveChannelConfiguration>
BBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetLiveChannelInfoResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $info = $result->getData();
        $this->assertEquals("test", $info->getDescription());
        $this->assertEquals("enabled", $info->getStatus());
        $this->assertEquals("HLS", $info->getType());
        $this->assertEquals(2, $info->getFragDuration());
        $this->assertEquals(3, $info->getFragCount());
        $this->assertEquals("playlist.m3u8", $info->getPlayListName());

    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), "", 200);
        $result = new GetLiveChannelInfoResult($response);
        $info = $result->getData();
        $this->assertEquals(null, $info->getDescription());
        $this->assertEquals(null, $info->getStatus());
        $this->assertEquals(null, $info->getType());
        $this->assertEquals(null, $info->getFragDuration());
        $this->assertEquals(null, $info->getFragCount());
        $this->assertEquals(null, $info->getPlayListName());
    }


    public function testParseValidXml2()
    {
        $response = new ResponseCore(array(), $this->validXml2, 200);
        $result = new GetLiveChannelInfoResult($response);
        $info = $result->getData();
        $this->assertEquals("test", $info->getDescription());
        $this->assertEquals("enabled", $info->getStatus());
        $this->assertEquals("HLS", $info->getType());
        $this->assertEquals(2, $info->getFragDuration());
        $this->assertEquals(3, $info->getFragCount());

        $this->assertEquals("snapshotnotify", $info->getSnapshot()->getNotifyTopic());
        $this->assertEquals("snapshotdest", $info->getSnapshot()->getDestBucket());
        $this->assertEquals("role_for_snapshot",$info->getSnapshot()->getRoleName());
        $this->assertEquals(1,$info->getSnapshot()->getInterval());
    }



}

