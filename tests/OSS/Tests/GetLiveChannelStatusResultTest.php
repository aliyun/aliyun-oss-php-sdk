<?php


namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetLiveChannelStatusResult;

class GetLiveChannelStatusResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<LiveChannelStat>
  <Status>Idle</Status>
</LiveChannelStat>
BBB;
    private $validXmlTwo = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<LiveChannelStat>
  <Status>Live</Status>
  <ConnectedTime>2016-08-25T06:25:15.000Z</ConnectedTime>
  <RemoteAddr>10.1.2.3:47745</RemoteAddr>
  <Video>
    <Width>1280</Width>
    <Height>536</Height>
    <FrameRate>24</FrameRate>
    <Bandwidth>0</Bandwidth>
    <Codec>H264</Codec>
  </Video>
  <Audio>
    <Bandwidth>0</Bandwidth>
    <SampleRate>44100</SampleRate>
    <Codec>ADPCM</Codec>
  </Audio>
</LiveChannelStat>
BBB;

    public function testParseValidXmlOne()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetLiveChannelStatusResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $status = $result->getData();
        $this->assertEquals("Idle",$status->getStatus());

    }

    public function testParseValidXmlTwo()
    {
        $response = new ResponseCore(array(), $this->validXmlTwo, 200);
        $result = new GetLiveChannelStatusResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $status = $result->getData();
        $this->assertEquals("Live",$status->getStatus());
        $this->assertEquals("2016-08-25T06:25:15.000Z",$status->getConnectedTime());
        $this->assertEquals("10.1.2.3:47745",$status->getRemoteAddr());
        $this->assertEquals(1280,$status->getVideoWidth());
        $this->assertEquals(536,$status->getVideoHeight());
        $this->assertEquals(24,$status->getVideoFrameRate());
        $this->assertEquals(0,$status->getAudioBandwidth());
        $this->assertEquals(44100,$status->getAudioSampleRate());
        $this->assertEquals("ADPCM",$status->getAudioCodec());

    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), "", 200);
        $result = new GetLiveChannelStatusResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $status = $result->getData();
        $this->assertEquals("",$status->getStatus());
        $this->assertEquals("",$status->getConnectedTime());
        $this->assertEquals("",$status->getRemoteAddr());
        $this->assertEquals(null,$status->getVideoWidth());
        $this->assertEquals(null,$status->getVideoHeight());
        $this->assertEquals(null,$status->getVideoFrameRate());
        $this->assertEquals(null,$status->getAudioBandwidth());
        $this->assertEquals(null,$status->getAudioSampleRate());
        $this->assertEquals(null,$status->getAudioCodec());
    }
}

