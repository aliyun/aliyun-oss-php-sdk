<?php
namespace OSS\Tests;

use OSS\Result\GetBucketResourceGroupResult;
use OSS\Http\ResponseCore;

class GetBucketResourceGroupResultTest extends \PHPUnit\Framework\TestCase
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
<ResourceGroupId></ResourceGroupId>
</BucketResourceGroupConfiguration>
BBBB;

    private $invalidXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketResourceGroupConfiguration>
</BucketResourceGroupConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketResourceGroupResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $id = $result->getData();
        $this->assertEquals("rg-xxxxxx", $id);
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetBucketResourceGroupResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $id = $result->getData();
        $this->assertEquals("", $id);
    }

    public function testParseInvalidXml2()
    {
        $response = new ResponseCore(array(), $this->invalidXml2, 200);
        $result = new GetBucketResourceGroupResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $id= $result->getData();
        $this->assertEquals(false, $id);
    }
}