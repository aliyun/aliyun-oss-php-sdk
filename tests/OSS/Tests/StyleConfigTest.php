<?php

namespace OSS\Tests;

use OSS\Model\StyleConfig;

class StyleConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<Style>
<Content>image/resize,p_50</Content>
</Style>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<Style/>
BBBB;

    public function testValidXml()
    {
        $styleConfig = new StyleConfig();
        $styleConfig->setContent('image/resize,p_50');
        $this->assertEquals($this->cleanXml($styleConfig->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXml()
    {
        $styleConfig = new StyleConfig();
        $styleConfig->parseFromXml($this->cleanXml($this->invalidXml));
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml($styleConfig->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
