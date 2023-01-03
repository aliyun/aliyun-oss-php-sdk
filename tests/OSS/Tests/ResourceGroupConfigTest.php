<?php
namespace OSS\Tests;

use OSS\Model\ResourceGroupConfig;

class ResourceGroupConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketResourceGroupConfiguration>
<ResourceGroupId>rg-xxxxxx</ResourceGroupId>
</BucketResourceGroupConfiguration>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketResourceGroupConfiguration>
<ResourceGroupId/>
</BucketResourceGroupConfiguration>
BBBB;

    private $invalidXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketResourceGroupConfiguration>
</BucketResourceGroupConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $resourceGroupConfig = new ResourceGroupConfig();
        $resourceGroupConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml(strval($resourceGroupConfig)));
        $this->assertEquals("rg-xxxxxx",$resourceGroupConfig->getResourceGroupId());
    }

    public function testValidXml1()
    {
        $resourceGroupConfig = new ResourceGroupConfig();
        $resourceGroupConfig->parseFromXml($this->validXml1);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml(strval($resourceGroupConfig)));
        $this->assertEquals("",$resourceGroupConfig->getResourceGroupId());
    }

    public function testInvalidXml1()
    {
        $resourceGroupConfig = new ResourceGroupConfig();
        $resourceGroupConfig->parseFromXml($this->invalidXml1);
        $this->assertEquals(null,$resourceGroupConfig->getResourceGroupId());
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}