<?php

namespace OSS\Tests;


use OSS\Model\RefererConfig;

class RefererConfigTest extends \PHPUnit\Framework\TestCase
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
<?xml version="1.0" encoding="utf-8"?>
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

    private $validXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<RefererConfiguration>
<AllowEmptyReferer>true</AllowEmptyReferer>
<RefererList/>
</RefererConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $refererConfig = new RefererConfig();
        $refererConfig->setAllowEmptyReferer(true);
        $refererConfig->addReferer("http://www.aliyun.com");
        $refererConfig->addReferer("https://www.aliyun.com");
        $refererConfig->addReferer("http://www.*.com");
        $refererConfig->addReferer("https://www.?.aliyuncs.com");

        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($refererConfig->serializeToXml()));
    }

    public function testParseValidXml1()
    {
        $refererConfig = new RefererConfig();
        $refererConfig->setAllowEmptyReferer(false);
        $refererConfig->setAllowTruncateQueryString(false);
        $refererConfig->addReferer("http://www.aliyun.com");
        $refererConfig->addReferer("https://www.aliyun.com");
        $refererConfig->addReferer("http://www.*.com");
        $refererConfig->addReferer("https://www.?.aliyuncs.com");

        $refererConfig->addBlackReferer("http://www.refuse.com");
        $refererConfig->addBlackReferer("https://*.hack.com");
        $refererConfig->addBlackReferer("http://ban.*.com");
        $refererConfig->addBlackReferer("https://www.?.deny.com");

        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($refererConfig->serializeToXml()));
    }

    public function testParseValidXml2()
    {
        $refererConfig = new RefererConfig();
        $refererConfig->setAllowEmptyReferer(true);
        $this->assertEquals($this->cleanXml($this->validXml2), $this->cleanXml(strval($refererConfig)));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
