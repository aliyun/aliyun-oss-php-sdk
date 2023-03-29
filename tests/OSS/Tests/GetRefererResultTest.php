<?php

namespace OSS\Tests;

use OSS\Result\GetRefererResult;
use OSS\Http\ResponseCore;
use OSS\Core\OssException;


class GetRefererResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<RefererConfiguration>
<AllowEmptyReferer>true</AllowEmptyReferer>
<RefererList>
<Referer>http://www.aliyun.com</Referer>
<Referer>https://www.aliyun.com</Referer>
<Referer>http://www.*.com</Referer>
<Referer>https://www.?.aliyuncs.com</Referer>
</RefererList>
</RefererConfiguration>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<RefererConfiguration>
<AllowEmptyReferer>true</AllowEmptyReferer>
<RefererList/>
</RefererConfiguration>
BBBB;

    private $validXml2 = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<RefererConfiguration>
<AllowEmptyReferer>false</AllowEmptyReferer>
<AllowTruncateQueryString>false</AllowTruncateQueryString>
<RefererList>
<Referer>http://www.aliyun.com</Referer>
<Referer>https://www.aliyun.com</Referer>
<Referer>http://www.*.com</Referer>
<Referer>https://www.?.aliyuncs.com</Referer>
</RefererList>
<RefererBlacklist>
<Referer>http://www.refuse.com</Referer>
<Referer>https://*.hack.com</Referer>
<Referer>http://ban.*.com</Referer>
<Referer>https://www.?.deny.com</Referer>
</RefererBlacklist>
</RefererConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetRefererResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $refererConfig = $result->getData();

        $this->assertTrue($refererConfig->getAllowEmptyReferer());
        $refererList = $refererConfig->getRefererList();

        $this->assertEquals(count($refererList),4);
        $this->assertEquals($refererList[0],"http://www.aliyun.com");
        $this->assertEquals($refererList[1],"https://www.aliyun.com");
        $this->assertEquals($refererList[2],"http://www.*.com");
        $this->assertEquals($refererList[3],"https://www.?.aliyuncs.com");

    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetRefererResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $refererConfig = $result->getData();

        $this->assertTrue($refererConfig->getAllowEmptyReferer());
        $refererList = $refererConfig->getRefererList();
        $this->assertEquals(count($refererList),0);

        $this->assertNull($refererConfig->getAllowTruncateQueryString());
        $this->assertNull($refererConfig->getRefererBlacklist());
    }

    public function testParseValidXml2()
    {
        $response = new ResponseCore(array(), $this->validXml2, 200);
        $result = new GetRefererResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $refererConfig = $result->getData();
        $this->assertFalse($refererConfig->getAllowEmptyReferer());
        $this->assertFalse($refererConfig->getAllowTruncateQueryString());
        $refererList = $refererConfig->getRefererList();

        $this->assertEquals(count($refererList),4);
        $this->assertEquals($refererList[0],"http://www.aliyun.com");
        $this->assertEquals($refererList[1],"https://www.aliyun.com");
        $this->assertEquals($refererList[2],"http://www.*.com");
        $this->assertEquals($refererList[3],"https://www.?.aliyuncs.com");

        $blacklist = $refererConfig->getRefererBlacklist();

        $this->assertEquals(count($blacklist),4);
        $this->assertEquals($blacklist[0],"http://www.refuse.com");
        $this->assertEquals($blacklist[1],"https://*.hack.com");
        $this->assertEquals($blacklist[2],"http://ban.*.com");
        $this->assertEquals($blacklist[3],"https://www.?.deny.com");



    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

    public function testInvalidResponse()
    {
        $response = new ResponseCore(array(), $this->validXml, 300);
        try {
            $result = new GetRefererResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }
}
