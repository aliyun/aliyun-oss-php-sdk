<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetBucketAccessPointResult;


class PutBucketAccessPointResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<CreateAccessPointResult>
<AccessPointArn>acs:oss:ap-southeast-2:128364106451****:accesspoint/ap-01</AccessPointArn>
<Alias>ap-01-45ee7945007a2f0bcb595f63e2215c****-ossalias</Alias>
</CreateAccessPointResult>
BBBB;

    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<CreateAccessPointResult>
</CreateAccessPointResult>
BBBB;
    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketAccessPointResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $config = $result->getData();
        $this->assertEquals("acs:oss:ap-southeast-2:128364106451****:accesspoint/ap-01", $config->getAccessPointArn());
        $this->assertEquals("ap-01-45ee7945007a2f0bcb595f63e2215c****-ossalias", $config->getAlias());
    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), $this->invalidXml, 200);
        $result = new GetBucketAccessPointResult($response);
        $config = $result->getData();
        $this->assertEquals(null, $config->getAccessPointArn());
        $this->assertEquals(null, $config->getAlias());

    }
}
