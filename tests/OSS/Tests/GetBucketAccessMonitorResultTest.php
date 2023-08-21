<?php

namespace OSS\Tests;

use OSS\Result\GetBucketAccessMonitorResult;
use OSS\Http\ResponseCore;

class GetBucketAccessMonitorResultTest extends \PHPUnit\Framework\TestCase
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

    private $invalidXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<AccessMonitorConfiguration>
</AccessMonitorConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketAccessMonitorResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $status = $result->getData();
        $this->assertEquals("Enabled", $status);
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetBucketAccessMonitorResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $status = $result->getData();
        $this->assertEquals("Disabled", $status);
    }

    public function testParseInvalidXml2()
    {
        $response = new ResponseCore(array(), $this->invalidXml2, 200);
        $result = new GetBucketTransferAccelerationResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);
        $status= $result->getData();
        $this->assertEquals(false, $status);
    }
}
