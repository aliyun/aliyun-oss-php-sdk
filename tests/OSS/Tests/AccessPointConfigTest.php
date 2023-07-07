<?php

namespace OSS\Tests;

use OSS\Model\AccessPointConfig;

class AccessPointConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<CreateAccessPointConfiguration>
<AccessPointName>ap-01</AccessPointName>
<NetworkOrigin>vpc</NetworkOrigin>
<VpcConfiguration>
<VpcId>vpc-t4nlw426y44rd3iq4****</VpcId>
</VpcConfiguration>
</CreateAccessPointConfiguration>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<CreateAccessPointConfiguration>
<AccessPointName>ap-01</AccessPointName>
<NetworkOrigin>internet</NetworkOrigin>
</CreateAccessPointConfiguration>
BBBB;

    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<CreateAccessPointConfiguration/>
BBBB;

    public function testParseValidXml()
    {
        $apName = "ap-01";
        $net = "vpc";
        $vpcId = "vpc-t4nlw426y44rd3iq4****";
        $accessConfig = new AccessPointConfig($apName,$net,$vpcId);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml(strval($accessConfig)));
    }

    public function testValidXml1()
    {
        $apName = "ap-01";
        $net = "internet";
        $accessConfig = new AccessPointConfig($apName,$net);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml(strval($accessConfig)));
    }

    public function testInvalidXml1()
    {
        $accessConfig = new AccessPointConfig();
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml(strval($accessConfig)));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
