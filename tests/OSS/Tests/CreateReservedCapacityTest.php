<?php

namespace OSS\Tests;


use OSS\Model\CreateReservedCapacity;

class CreateReservedCapacityTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityConfiguration>
<Name>your-rc-name</Name>
<DataRedundancyType>LRS</DataRedundancyType>
<ReservedCapacity>10240</ReservedCapacity>
</ReservedCapacityConfiguration>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReservedCapacityConfiguration>
<Name>your-rc-name</Name>
<ReservedCapacity>10240</ReservedCapacity>
</ReservedCapacityConfiguration>
BBBB;


    public function testParseValidXml()
    {
        $name = "your-rc-name";
        $dataRedundancyType = "LRS";
        $reservedCapacity = 10240;
        $config = new CreateReservedCapacity($name,$dataRedundancyType,$reservedCapacity);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($config->serializeToXml()));
    }

    public function testParseValidXml1()
    {
        $name = "your-rc-name";
        $reservedCapacity = 10240;
        $config = new CreateReservedCapacity($name,null,$reservedCapacity);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($config->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
