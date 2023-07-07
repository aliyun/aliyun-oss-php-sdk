<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\GetBucketAccessPointResult;


class GetBucketAccessPointResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<GetAccessPointResult>
<AccessPointName>ap-01</AccessPointName>
<Bucket>oss-example</Bucket>
<AccountId>111933544165****</AccountId>
<NetworkOrigin>vpc</NetworkOrigin>
<VpcConfiguration>
<VpcId>vpc-t4nlw426y44rd3iq4****</VpcId>
</VpcConfiguration>
<AccessPointArn>arn:acs:oss:ap-southeast-2:111933544165****:accesspoint/ap-01</AccessPointArn>
<CreationDate>1626769503</CreationDate>
<Alias>ap-01-ossalias</Alias>
<Status>enable</Status>
<Endpoints>
<PublicEndpoint>ap-01.oss-ap-southeast-2.oss-accesspoint.aliyuncs.com</PublicEndpoint>
<InternalEndpoint>ap-01.oss-ap-southeast-2-internal.oss-accesspoint.aliyuncs.com</InternalEndpoint>
</Endpoints>
</GetAccessPointResult>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<GetAccessPointResult>
<AccessPointName>ap-01</AccessPointName>
<Bucket>oss-example</Bucket>
<AccountId>111933544165****</AccountId>
<NetworkOrigin>internet</NetworkOrigin>
<VpcConfiguration>
<VpcId></VpcId>
</VpcConfiguration>
<AccessPointArn>arn:acs:oss:ap-southeast-2:111933544165****:accesspoint/ap-01</AccessPointArn>
<CreationDate>1626769503</CreationDate>
<Alias>ap-01-ossalias</Alias>
<Status>enable</Status>
<Endpoints>
<PublicEndpoint>ap-01.oss-ap-southeast-2.oss-accesspoint.aliyuncs.com</PublicEndpoint>
<InternalEndpoint>ap-01.oss-ap-southeast-2-internal.oss-accesspoint.aliyuncs.com</InternalEndpoint>
</Endpoints>
</GetAccessPointResult>
BBBB;

    private $nullXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<GetAccessPointResult/>
BBBB;
    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketAccessPointResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $config = $result->getData();
        $this->assertEquals("ap-01", $config->getAccessPointName());
        $this->assertEquals("oss-example", $config->getBucket());
        $this->assertEquals("111933544165****", $config->getAccountId());
        $this->assertEquals("vpc", $config->getNetworkOrigin());
        $this->assertEquals("vpc-t4nlw426y44rd3iq4****", $config->getVpcId());
        $this->assertEquals("arn:acs:oss:ap-southeast-2:111933544165****:accesspoint/ap-01", $config->getAccessPointArn());
        $this->assertEquals("1626769503", $config->getCreationDate());
        $this->assertEquals("ap-01-ossalias", $config->getAlias());
        $this->assertEquals("enable", $config->getStatus());
        $this->assertEquals("ap-01.oss-ap-southeast-2.oss-accesspoint.aliyuncs.com", $config->getPublicEndpoint());
        $this->assertEquals("ap-01.oss-ap-southeast-2-internal.oss-accesspoint.aliyuncs.com", $config->getInternalEndpoint());
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetBucketAccessPointResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $config = $result->getData();
        $this->assertEquals("ap-01", $config->getAccessPointName());
        $this->assertEquals("oss-example", $config->getBucket());
        $this->assertEquals("111933544165****", $config->getAccountId());
        $this->assertEquals("internet", $config->getNetworkOrigin());
        $this->assertEquals(null, $config->getVpcId());
        $this->assertEquals("arn:acs:oss:ap-southeast-2:111933544165****:accesspoint/ap-01", $config->getAccessPointArn());
        $this->assertEquals("1626769503", $config->getCreationDate());
        $this->assertEquals("ap-01-ossalias", $config->getAlias());
        $this->assertEquals("enable", $config->getStatus());
        $this->assertEquals("ap-01.oss-ap-southeast-2.oss-accesspoint.aliyuncs.com", $config->getPublicEndpoint());
        $this->assertEquals("ap-01.oss-ap-southeast-2-internal.oss-accesspoint.aliyuncs.com", $config->getInternalEndpoint());
    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), $this->nullXml, 200);
        $result = new GetBucketAccessPointResult($response);
        $config = $result->getData();
        $this->assertEquals(null, $config->getAccessPointName());
        $this->assertEquals(null, $config->getBucket());
        $this->assertEquals(null, $config->getAccountId());
        $this->assertEquals(null, $config->getNetworkOrigin());
        $this->assertEquals(null, $config->getVpcId());
        $this->assertEquals(null, $config->getAccessPointArn());
        $this->assertEquals(null, $config->getCreationDate());
        $this->assertEquals(null, $config->getAlias());
        $this->assertEquals(null, $config->getStatus());
        $this->assertEquals(null, $config->getPublicEndpoint());
        $this->assertEquals(null, $config->getInternalEndpoint());

    }
}
