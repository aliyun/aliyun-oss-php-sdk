<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\ListBucketAccessPointResult;

class ListBucketAccessPointResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8" ?>
<ListAccessPointsResult>
<IsTruncated>true</IsTruncated>
<NextContinuationToken>abc</NextContinuationToken>
<AccountId>111933544165****</AccountId>
<AccessPoints>
<AccessPoint>
<Bucket>oss-example</Bucket>
<AccessPointName>ap-01</AccessPointName>
<Alias>ap-01-ossalias</Alias>
<NetworkOrigin>vpc</NetworkOrigin>
<VpcConfiguration>
<VpcId>vpc-t4nlw426y44rd3iq4****</VpcId>
</VpcConfiguration>
<Status>enable</Status>
</AccessPoint>
<AccessPoint>
<AccessPointName>ap-02</AccessPointName>
<Alias>access-point-name-2-1280*****-ossalias</Alias>
<Bucket>oss-example</Bucket>
<NetworkOrigin>internet</NetworkOrigin>
<VpcConfiguration><VpcId></VpcId></VpcConfiguration>
<Status>enable</Status>
</AccessPoint>
</AccessPoints>
</ListAccessPointsResult>
BBBB;

    private $nullXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<GetAccessPointResult/>
BBBB;
    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListBucketAccessPointResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $list = $result->getData();
        $this->assertEquals(true, $list->getIsTruncated());
        $this->assertEquals("111933544165****", $list->getAccountId());
        $this->assertEquals("abc", $list->getNextContinuationToken());
        $accessPoints = $list->getAccessPoints();
        $this->assertEquals(2, count($accessPoints));
        $this->assertEquals("ap-01", $accessPoints[0]->getAccessPointName());
        $this->assertEquals("oss-example", $accessPoints[0]->getBucket());
        $this->assertEquals("ap-01-ossalias", $accessPoints[0]->getAlias());
        $this->assertEquals("vpc-t4nlw426y44rd3iq4****", $accessPoints[0]->getVpcId());
        $this->assertEquals("vpc", $accessPoints[0]->getNetworkOrigin());
        $this->assertEquals("enable", $accessPoints[0]->getStatus());

        $this->assertEquals("ap-02", $accessPoints[1]->getAccessPointName());
        $this->assertEquals("oss-example", $accessPoints[1]->getBucket());
        $this->assertEquals("access-point-name-2-1280*****-ossalias", $accessPoints[1]->getAlias());
        $this->assertEquals(null, $accessPoints[1]->getVpcId());
        $this->assertEquals("internet", $accessPoints[1]->getNetworkOrigin());
        $this->assertEquals("enable", $accessPoints[1]->getStatus());
    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), $this->nullXml, 200);
        $result = new ListBucketAccessPointResult($response);
        $list = $result->getData();
        $this->assertEquals(null, $list->getIsTruncated());
        $this->assertEquals(null, $list->getAccountId());
        $this->assertEquals(null, $list->getNextContinuationToken());
        $accessPoints = $list->getAccessPoints();
        $this->assertEquals(null, $accessPoints);

    }
}
