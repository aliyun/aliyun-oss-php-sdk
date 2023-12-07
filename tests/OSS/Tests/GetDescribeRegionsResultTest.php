<?php

namespace OSS\Tests;

use OSS\Result\GetBucketTransferAccelerationResult;
use OSS\Http\ResponseCore;
use OSS\Result\GetDescribeRegionsResult;

class GetDescribeRegionsResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<RegionInfoList>
<RegionInfo>
<Region>oss-cn-hangzhou</Region>
<InternetEndpoint>oss-cn-hangzhou.aliyuncs.com</InternetEndpoint>
<InternalEndpoint>oss-cn-hangzhou-internal.aliyuncs.com</InternalEndpoint>
<AccelerateEndpoint>oss-accelerate.aliyuncs.com</AccelerateEndpoint>  
</RegionInfo>
<RegionInfo>
<Region>oss-cn-shanghai</Region>
<InternetEndpoint>oss-cn-shanghai.aliyuncs.com</InternetEndpoint>
<InternalEndpoint>oss-cn-shanghai-internal.aliyuncs.com</InternalEndpoint>
<AccelerateEndpoint>oss-accelerate.aliyuncs.com</AccelerateEndpoint>  
</RegionInfo>
</RegionInfoList>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<RegionInfoList>
<RegionInfo>
<Region>oss-cn-hangzhou</Region>
<InternetEndpoint>oss-cn-hangzhou.aliyuncs.com</InternetEndpoint>
<InternalEndpoint>oss-cn-hangzhou-internal.aliyuncs.com</InternalEndpoint>
<AccelerateEndpoint>oss-accelerate.aliyuncs.com</AccelerateEndpoint>  
</RegionInfo>
</RegionInfoList>
BBBB;

    private $invalidXml2 = <<<BBBB
<?xml version="1.0" ?>
<RegionInfoList>
</RegionInfoList>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetDescribeRegionsResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $result = $result->getData();
        $this->assertEquals(count($result->getRegionInfoList()), 2);

        $list = $result->getRegionInfoList();
        $this->assertEquals($list[0]->getRegion(), "oss-cn-hangzhou");
        $this->assertEquals($list[0]->getRegion(), "oss-cn-hangzhou");
        $this->assertEquals($list[0]->getInternetEndpoint(), "oss-cn-hangzhou.aliyuncs.com");
        $this->assertEquals($list[0]->getInternalEndpoint(), "oss-cn-hangzhou-internal.aliyuncs.com");
        $this->assertEquals($list[0]->getAccelerateEndpoint(), "oss-accelerate.aliyuncs.com");

        $this->assertEquals($list[1]->getRegion(), "oss-cn-shanghai");
        $this->assertEquals($list[1]->getInternetEndpoint(), "oss-cn-shanghai.aliyuncs.com");
        $this->assertEquals($list[1]->getInternalEndpoint(), "oss-cn-shanghai-internal.aliyuncs.com");
        $this->assertEquals($list[1]->getAccelerateEndpoint(), "oss-accelerate.aliyuncs.com");
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetDescribeRegionsResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $result = $result->getData();
        $this->assertEquals(count($result->getRegionInfoList()), 1);

        $list = $result->getRegionInfoList();
        $this->assertEquals($list[0]->getRegion(), "oss-cn-hangzhou");
        $this->assertEquals($list[0]->getInternetEndpoint(), "oss-cn-hangzhou.aliyuncs.com");
        $this->assertEquals($list[0]->getInternalEndpoint(), "oss-cn-hangzhou-internal.aliyuncs.com");
        $this->assertEquals($list[0]->getAccelerateEndpoint(), "oss-accelerate.aliyuncs.com");
    }

    public function testParseInvalidXml2()
    {
        $response = new ResponseCore(array(), $this->invalidXml2, 200);
        $result = new GetDescribeRegionsResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $this->assertNotNull($result->getRawResponse()->body);


        $result = $result->getData();
        $this->assertNull($result->getRegionInfoList());
    }
}
