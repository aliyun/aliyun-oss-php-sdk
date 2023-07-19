<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\LoggingConfig;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientDescribeRegionsTest extends TestOssClientBase
{
    public function testDescribeRegions()
    {
        try {
            $list = $this->ossClient->getDescribeRegions();
            $this->assertNotNull($list->getRegionInfoList());
            $this->assertTrue(count($list->getRegionInfoList()) > 0);
        } catch (OssException $e) {
            var_dump($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $options['regions'] = 'oss-cn-hangzhou';
            $result = $this->ossClient->getDescribeRegions($options);
            $this->assertNotNull($result->getRegionInfoList());
            $this->assertTrue(count($result->getRegionInfoList()) > 0);
            $list = $result->getRegionInfoList();
            $this->assertEquals($list[0]->getRegion(), "oss-cn-hangzhou");
            $this->assertEquals($list[0]->getRegion(), "oss-cn-hangzhou");
            $this->assertEquals($list[0]->getInternetEndpoint(), "oss-cn-hangzhou.aliyuncs.com");
            $this->assertEquals($list[0]->getInternalEndpoint(), "oss-cn-hangzhou-internal.aliyuncs.com");
            $this->assertEquals($list[0]->getAccelerateEndpoint(), "oss-accelerate.aliyuncs.com");
        } catch (OssException $e) {
            $this->assertTrue(false);
        }
    }
}
