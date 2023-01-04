<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\ListBucketStyleResult;

class ListBucketStyleResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<StyleList>
<Style>
<Name>imagestyle</Name>
<Content>image/resize,p_50</Content>
<CreateTime>Wed, 20 May 2020 12:07:15 GMT</CreateTime>
<LastModifyTime>Wed, 21 May 2020 12:07:15 GMT</LastModifyTime>
</Style>
<Style>
<Name>imagestyle1</Name>
<Content>image/resize,w_200</Content>
<CreateTime>Wed, 20 May 2020 12:08:04 GMT</CreateTime>
<LastModifyTime>Wed, 21 May 2020 12:08:04 GMT</LastModifyTime>
</Style>
<Style>
<Name>imagestyle2</Name>
<Content>image/resize,w_300</Content>
<CreateTime>Fri, 12 Mar 2021 06:19:13 GMT</CreateTime>
<LastModifyTime>Fri, 13 Mar 2021 06:27:21 GMT</LastModifyTime>
</Style>
</StyleList>
BBBB;

    private $nullXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<StyleList>
<Style>
</Style>
</StyleList>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListBucketStyleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $listInfo = $result->getData();
        $this->assertEquals(3, count($listInfo->getStyleList()));
        $styleList = $listInfo->getStyleList();
        $this->assertEquals("imagestyle", $styleList[0]->getName());
        $this->assertEquals("imagestyle1", $styleList[1]->getName());
        $this->assertEquals("imagestyle2", $styleList[2]->getName());
        $this->assertEquals("image/resize,w_300", $styleList[2]->getContent());
        $this->assertEquals("Fri, 13 Mar 2021 06:27:21 GMT", $styleList[2]->getLastModifyTime());

    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), $this->nullXml, 200);
        $result = new ListBucketStyleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $listInfo = $result->getData();
        $this->assertEquals(0, count($listInfo->getStyleList()));
    }
}
