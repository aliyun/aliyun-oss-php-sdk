<?php

namespace OSS\Result;

use OSS\Model\ObjectInfo;
use OSS\Model\ObjectListInfo;
use OSS\Model\PrefixInfo;

/**
 * Class ListObjectsResult
 * @package OSS\Result
 */
class ListObjectsResult extends Result
{
    /**
     * 解析ListObjects接口返回的xml数据
     *
     * return ObjectListInfo
     */
    protected function parseDataFromResponse()
    {
        $xml = new \SimpleXMLElement($this->rawResponse->body);
        $objectList = $this->parseObjectList($xml);
        $prefixList = $this->parsePrefixList($xml);
        $bucketName = isset($xml->Name) ? strval($xml->Name) : "";
        $prefix = isset($xml->Prefix) ? strval($xml->Prefix) : "";
        $marker = isset($xml->Marker) ? strval($xml->Marker) : "";
        $maxKeys = isset($xml->MaxKeys) ? intval($xml->MaxKeys) : 0;
        $delimiter = isset($xml->Delimiter) ? strval($xml->Delimiter) : "";
        $isTruncated = isset($xml->IsTruncated) ? strval($xml->IsTruncated) : "";
        $nextMarker = isset($xml->NextMarker) ? strval($xml->NextMarker) : "";
        return new ObjectListInfo($bucketName, $prefix, $marker, $nextMarker, $maxKeys, $delimiter, $isTruncated, $objectList, $prefixList);
    }

    private function parseObjectList($xml)
    {
        $retList = array();
        if (isset($xml->Contents)) {
            foreach ($xml->Contents as $content) {
                $key = isset($content->Key) ? strval($content->Key) : "";
                $lastModified = isset($content->LastModified) ? strval($content->LastModified) : "";
                $eTag = isset($content->ETag) ? strval($content->ETag) : "";
                $type = isset($content->Type) ? strval($content->Type) : "";
                $size = isset($content->Size) ? intval($content->Size) : 0;
                $storageClass = isset($content->StorageClass) ? strval($content->StorageClass) : "";
                $retList[] = new ObjectInfo($key, $lastModified, $eTag, $type, $size, $storageClass);
            }
        }
        return $retList;
    }

    private function parsePrefixList($xml)
    {
        $retList = array();
        if (isset($xml->CommonPrefixes)) {
            foreach ($xml->CommonPrefixes as $commonPrefix) {
                $prefix = isset($commonPrefix->Prefix) ? strval($commonPrefix->Prefix) : "";
                $retList[] = new PrefixInfo($prefix);
            }
        }
        return $retList;
    }
}