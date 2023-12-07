<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\RegionInfoList;


/**
 * Class GetDescribeRegionsResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/345596.html
 */
class GetDescribeRegionsResult extends Result
{
    /**
     * @return RegionInfoList
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (!isset($content) || $content === "") {
            throw new OssException("body is null");
        }
        $list= new RegionInfoList();
        $list->parseFromXml($content);
        return $list;
    }
}