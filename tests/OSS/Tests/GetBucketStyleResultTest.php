<?php

namespace OSS\Tests;

use OSS\Result\GetBucketStyleResult;
use OSS\Http\ResponseCore;

class GetBucketStyleResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<Style>
<Name>imagestyle</Name>
<Content>image/resize,p_50</Content>
<CreateTime>Wed, 20 May 2020 12:07:15 GMT</CreateTime>
<LastModifyTime>Wed, 21 May 2020 12:07:15 GMT</LastModifyTime>
</Style>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<Style>
<Name>imagestyle2</Name>
<Content>image/resize,p_100</Content>
<CreateTime>Wed, 20 May 2020 12:07:15 GMT</CreateTime>
<LastModifyTime>Wed, 21 May 2020 12:07:15 GMT</LastModifyTime>
</Style>
BBBB;

    private $invalidXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<Style>
</Style>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketStyleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $rs = $result->getData();
        $this->assertEquals("imagestyle", $rs->getName());
        $this->assertEquals("image/resize,p_50", $rs->getContent());
        $this->assertEquals("Wed, 20 May 2020 12:07:15 GMT", $rs->getCreateTime());
        $this->assertEquals("Wed, 21 May 2020 12:07:15 GMT", $rs->getLastModifyTime());
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetBucketStyleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $rs = $result->getData();
        $this->assertEquals("imagestyle2", $rs->getName());
        $this->assertEquals("image/resize,p_100", $rs->getContent());
        $this->assertEquals("Wed, 20 May 2020 12:07:15 GMT", $rs->getCreateTime());
        $this->assertEquals("Wed, 21 May 2020 12:07:15 GMT", $rs->getLastModifyTime());
    }

    public function testParseInvalidXml2()
    {
        $response = new ResponseCore(array(), $this->invalidXml2, 200);
        $result = new GetBucketStyleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $rs= $result->getData();
        $this->assertNull($rs->getName());
        $this->assertNull($rs->getContent());
        $this->assertNull($rs->getCreateTime());
        $this->assertNull($rs->getLastModifyTime());
    }
}