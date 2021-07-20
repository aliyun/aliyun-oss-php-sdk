<?php

namespace OSS\Result;

use OSS\Model\InventoryInfo;

/**
 * Class ListObjectsResult
 * @package OSS\Result
 */
class ListBucketInventoryResult extends Result
{
    /**
     * Parse the xml data returned by the ListObjects interface
     *
     * return ObjectListInfo
     */
    protected function parseDataFromResponse()
    {
        $xml = new \SimpleXMLElement($this->rawResponse->body);
        return $inventoryList = $this->parseInventoryList($xml);
    }

    private function parseInventoryList($xml)
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