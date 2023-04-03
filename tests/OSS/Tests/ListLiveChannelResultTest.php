<?php
namespace OSS\Tests;
use OSS\Http\ResponseCore;
use OSS\Result\ListLiveChannelResult;

class ListLiveChannelResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<ListLiveChannelResult>
<Prefix>prefix</Prefix>
<Marker>/</Marker>
<MaxKeys>1</MaxKeys>
<IsTruncated>true</IsTruncated>
<NextMarker>channel-0</NextMarker>
<LiveChannel>
<Name>channel-0</Name>
<Description></Description>
<Status>disabled</Status>
<LastModified>2016-07-30T01:54:21.000Z</LastModified>
<PublishUrls>
<Url>rtmp://test-bucket.oss-cn-hangzhou.aliyuncs.com/live/channel-0</Url>
</PublishUrls>
<PlayUrls>
<Url>http://test-bucket.oss-cn-hangzhou.aliyuncs.com/channel-0/playlist.m3u8</Url>
</PlayUrls>
</LiveChannel>
</ListLiveChannelResult>
BBB;

    private $validXml1 = <<<BBB
<?xml version="1.0" encoding="UTF-8"?>
<ListLiveChannelResult>
<Prefix></Prefix>
<Marker></Marker>
<MaxKeys></MaxKeys>
</ListLiveChannelResult>
BBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListLiveChannelResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $list = $result->getData();
        $this->assertEquals("prefix",$list->getPrefix());
        $this->assertEquals("/",$list->getMarker());
        $this->assertEquals(1,$list->getMaxKeys());
        $this->assertEquals("true",$list->getIsTruncated());
        $this->assertEquals("channel-0",$list->getNextMarker());
        var_dump($list->getChannelList());
        $this->assertEquals("http://test-bucket.oss-cn-hangzhou.aliyuncs.com/channel-0/playlist.m3u8",$list->getLiveChannel()[0]->getPlayUrls()[0]);

    }
    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new ListLiveChannelResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $list = $result->getData();
        $this->assertEquals(null,$list->getPrefix());
        $this->assertEquals(null,$list->getMarker());
        $this->assertEquals(null,$list->getMaxKeys());
        $this->assertEquals(null,$list->getChannelList());
    }

}
