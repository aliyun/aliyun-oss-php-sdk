<?php

namespace OSS\Result;

use OSS\Model\InventoryInfo;
use OSS\Model\InventoryListInfo;

/**
 * Class ListObjectsResult
 * @package OSS\Result
 */
class ListBucketInventoryResult extends Result
{
    /**
     * Parse the xml data returned by the ListObjects interface
     *
     * return InventoryListInfo
     */
    protected function parseDataFromResponse()
    {
        $xml = new \SimpleXMLElement($this->rawResponse->body);
        $encodingType = isset($xml->EncodingType) ? strval($xml->EncodingType) : "";
        $inventoryList = $this->parseInventoryList($xml,$encodingType);
        $isTruncated = isset($xml->IsTruncated) ? strval($xml->IsTruncated) : "";
        $nextContinuationToken = isset($xml->NextContinuationToken) ? strval($xml->NextContinuationToken) : "";
        return new InventoryListInfo($isTruncated,$nextContinuationToken,$inventoryList);
    }

    private function parseInventoryList($xml,$encodingType)
    {
        $invList = array();
        if (isset($xml->InventoryConfiguration)) {
            foreach ($xml->InventoryConfiguration as $info) {
                $id = isset($info->Id) ? strval($info->Id) : "";
                $isEnabled = isset($info->IsEnabled) ? strval($info->IsEnabled) : "";
                $destination = isset($info->Destination) ? json_decode(json_encode($info->Destination,LIBXML_NOCDATA),true) : "";
                $schedule = isset($info->Schedule) ? json_decode(json_encode($info->Schedule,LIBXML_NOCDATA),true) : "";
                $filter = isset($info->Filter) ? json_decode(json_encode($info->Filter,LIBXML_NOCDATA),true) : "";
                $includedObjectVersions = isset($info->IncludedObjectVersions) ? strval($info->IncludedObjectVersions) : "";
                $optionalFields = isset($info->OptionalFields) ? json_decode(json_encode($info->OptionalFields,LIBXML_NOCDATA),true) : "";
                $invList[] = new InventoryInfo($id, $isEnabled, $destination,$schedule,$filter,$includedObjectVersions,$optionalFields);
            }
        }
        return $invList;
    }
}