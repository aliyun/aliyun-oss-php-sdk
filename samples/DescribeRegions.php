<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//*******************************Simple Usage ***************************************************************

// list all regions
$result = $ossClient->getDescribeRegions();
if ($result->getRegionInfoList() !== null){
    foreach ($result->getRegionInfoList() as $region){
        printf("Region:".$region->getRegion().PHP_EOL);
        printf("Region Internet Endpoint:".$region->getInternetEndpoint().PHP_EOL);
        printf("Region Internal Endpoint:".$region->getInternalEndpoint().PHP_EOL);
        printf("Region Accelerate Endpoint:".$region->getAccelerateEndpoint().PHP_EOL);
    }
}

// get region by endpoint
$options['regions'] = 'oss-cn-hangzhou';
$result = $ossClient->getDescribeRegions($options);
if ($result->getRegionInfoList() !== null){
    foreach ($result->getRegionInfoList() as $region){
        printf("Region:".$region->getRegion().PHP_EOL);
        printf("Region Internet Endpoint:".$region->getInternetEndpoint().PHP_EOL);
        printf("Region Internal Endpoint:".$region->getInternalEndpoint().PHP_EOL);
        printf("Region Accelerate Endpoint:".$region->getAccelerateEndpoint().PHP_EOL);
    }
}

//******************************* For complete usage, see the following functions ****************************************************

getDescribeRegions($ossClient);
listDescribeRegions($ossClient);

/**
 * Set bucket logging configuration
 *
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket bucket name
 * @return null
 */

/**
 * Get Describe Regions
 * @param OssClient $ossClient OssClient instance
 * @throws RequestCore_Exception
 */
function getDescribeRegions($ossClient)
{
    try {
        $options['regions'] = 'oss-cn-hangzhou';
        $result = $ossClient->getDescribeRegions($options);
        if ($result->getRegionInfoList() !== null){
            foreach ($result->getRegionInfoList() as $region){
                printf("Region:".$region->getRegion().PHP_EOL);
                printf("Region Internet Endpoint:".$region->getInternetEndpoint().PHP_EOL);
                printf("Region Internal Endpoint:".$region->getInternalEndpoint().PHP_EOL);
                printf("Region Accelerate Endpoint:".$region->getAccelerateEndpoint().PHP_EOL);
            }
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * List Describe Regions
 *
 * @param OssClient $ossClient OssClient instance
 * @throws RequestCore_Exception
 */
function listDescribeRegions($ossClient)
{
    try {
        $result = $ossClient->getDescribeRegions();
        if ($result->getRegionInfoList() !== null){
            foreach ($result->getRegionInfoList() as $region){
                printf("Region:".$region->getRegion().PHP_EOL);
                printf("Region Internet Endpoint:".$region->getInternetEndpoint().PHP_EOL);
                printf("Region Internal Endpoint:".$region->getInternalEndpoint().PHP_EOL);
                printf("Region Accelerate Endpoint:".$region->getAccelerateEndpoint().PHP_EOL);
            }
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}