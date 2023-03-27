<?php

namespace OSS\Tests;


use OSS\Model\UpdateReservedCapacity;

class UpdateReservedCapacityTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityConfiguration>
<Status>Enabled</Status>
<ReservedCapacity>10240</ReservedCapacity>
<AutoExpansionSize>100</AutoExpansionSize>
<AutoExpansionMaxSize>20480</AutoExpansionMaxSize>
</ReservedCapacityConfiguration>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityConfiguration>
<Status>Enabled</Status>
</ReservedCapacityConfiguration>
BBBB;

    private $validXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityConfiguration/>
BBBB;

    public function testParseValidXml()
    {
        $status = "Enabled";
        $reservedCapacity = 10240;
        $autoExpansionSize = 100;
        $autoExpansionMaxSize = 20480;
        $update = new UpdateReservedCapacity($status,$reservedCapacity,$autoExpansionSize,$autoExpansionMaxSize);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($update->serializeToXml()));
    }

    public function testParseValidXml1()
    {
        $status = "Enabled";
        $config = new UpdateReservedCapacity($status);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($config->serializeToXml()));
    }

    public function testParseValidXml2()
    {
        $config = new UpdateReservedCapacity();
        $this->assertEquals($this->cleanXml($this->validXml2), $this->cleanXml($config->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
