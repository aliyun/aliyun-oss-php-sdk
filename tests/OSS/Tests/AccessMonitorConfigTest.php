<?php

namespace OSS\Tests;

use OSS\Model\AccessMonitorConfig;
use phpDocumentor\Reflection\Types\Null_;

class AccessMonitorConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<AccessMonitorConfiguration>
<Status>Enabled</Status>
</AccessMonitorConfiguration>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<AccessMonitorConfiguration>
<Status>Disabled</Status>
</AccessMonitorConfiguration>
BBBB;

    private $invalidXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<AccessMonitorConfiguration>
</AccessMonitorConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $accessConfig = new AccessMonitorConfig();
        $accessConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml(strval($accessConfig)));
        $this->assertEquals("Enabled",$accessConfig->getStatus());
    }

    public function testValidXml1()
    {
        $accessConfig = new AccessMonitorConfig();
        $accessConfig->parseFromXml($this->validXml1);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml(strval($accessConfig)));
        $this->assertEquals("Disabled",$accessConfig->getStatus());
    }

    public function testInvalidXml1()
    {
        $accessConfig = new AccessMonitorConfig();
        $accessConfig->parseFromXml($this->invalidXml1);
        $this->assertEquals(null,$accessConfig->getStatus());
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
