<?php
namespace OSS\Tests;
use OSS\Http\ResponseCore;
use OSS\Result\PutLiveChannelResult;

class PutLiveChannelResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<CreateLiveChannelResult>
  <PublishUrls>
    <Url>rtmp://test-bucket.oss-cn-hangzhou.aliyuncs.com/live/test-channel</Url>
  </PublishUrls>
  <PlayUrls>
    <Url>http://test-bucket.oss-cn-hangzhou.aliyuncs.com/test-channel/playlist.m3u8</Url>
  </PlayUrls>
</CreateLiveChannelResult>
BBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new PutLiveChannelResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertEquals("rtmp://test-bucket.oss-cn-hangzhou.aliyuncs.com/live/test-channel",$result->getData()->getPublishUrls()[0]);
        $this->assertEquals("http://test-bucket.oss-cn-hangzhou.aliyuncs.com/test-channel/playlist.m3u8",$result->getData()->getPlayUrls()[0]);

    }
    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), "", 200);
        $result = new PutLiveChannelResult($response);
        $this->assertEquals(null, $result->getData()->getPublishUrls()[0]);
        $this->assertEquals(null, $result->getData()->getPublishUrls()[0]);
    }
}
