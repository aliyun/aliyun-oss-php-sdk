<?php

namespace OSS\Tests;

use OSS\Model\ListStyleConfig;
use OSS\Model\StyleConfig;

class ListStyleConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
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
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<StyleList/>
BBBB;

    public function testValidXml()
    {
        $listStyleConfig = new ListStyleConfig();

        $listStyleConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($listStyleConfig->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXml()
    {
        $listStyleConfig = new ListStyleConfig();
        $listStyleConfig->parseFromXml($this->invalidXml);
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml($listStyleConfig->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
