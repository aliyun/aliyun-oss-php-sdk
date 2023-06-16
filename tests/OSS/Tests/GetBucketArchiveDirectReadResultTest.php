<?php

namespace OSS\Tests;

use OSS\Result\GetBucketArchiveDirectReadResult;
use OSS\Http\ResponseCore;

class GetBucketArchiveDirectReadResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<ArchiveDirectReadConfiguration>
<Enabled>true</Enabled>
</ArchiveDirectReadConfiguration>
BBBB;
    private $validXml1 = <<<BBBB
<ArchiveDirectReadConfiguration>
<Enabled>false</Enabled>
</ArchiveDirectReadConfiguration>
BBBB;

    private $invalidXml2 = <<<BBBB
<?xml version="1.0" ?>
<TransferAccelerationConfiguration>
</TransferAccelerationConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketArchiveDirectReadResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $enabled = $result->getData();
        $this->assertEquals(true, $enabled);
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetBucketArchiveDirectReadResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $enabled = $result->getData();
        $this->assertEquals(false, $enabled);
    }

    public function testParseInvalidXml2()
    {
        $response = new ResponseCore(array(), $this->invalidXml2, 200);
        $result = new GetBucketArchiveDirectReadResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $enabled = $result->getData();
        $this->assertEquals(false, $enabled);
    }
}
