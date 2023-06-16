<?php

namespace OSS\Tests;

use OSS\Model\ArchiveDirectReadConfig;

class ArchiveDirectReadConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ArchiveDirectReadConfiguration>
<Enabled>true</Enabled>
</ArchiveDirectReadConfiguration>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ArchiveDirectReadConfiguration>
<Enabled>false</Enabled>
</ArchiveDirectReadConfiguration>
BBBB;

    private $invalidXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ArchiveDirectReadConfiguration>
</ArchiveDirectReadConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $transferConfig = new ArchiveDirectReadConfig();
        $transferConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml(strval($transferConfig)));
        $this->assertEquals(true,$transferConfig->getEnabled());
    }

    public function testValidXml1()
    {
        $transferConfig = new ArchiveDirectReadConfig();
        $transferConfig->parseFromXml($this->validXml1);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml(strval($transferConfig)));
        $this->assertEquals(false,$transferConfig->getEnabled());
    }

    public function testInvalidXml1()
    {
        $transferConfig = new ArchiveDirectReadConfig();
        $transferConfig->parseFromXml($this->invalidXml1);
        $this->assertEquals(false,$transferConfig->getEnabled());
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
