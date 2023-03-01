<?php

namespace OSS\Result;
use OSS\Core\OssUtil;
use OSS\Model\MetaQueryAggregation;
use OSS\Model\MetaQueryFiles;
use OSS\Model\DoMetaQuery;
use OSS\Model\MetaQueryGroup;
use OSS\Model\MetaQueryTagging;
use OSS\Model\MetaQueryUserMeta;

/**
 * Class DoMetaQueryResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/419228.html
 */
class DoMetaQueryResult extends Result
{
    /**
     * @return DoMetaQuery
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $doMetaQuery = new DoMetaQuery();
        $doMetaQuery->parseFromXml($content);
        return $doMetaQuery;
    }



}