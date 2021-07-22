<?php

namespace OSS\Result;
use OSS\Model\InventoryInfo;

/**
 * Class GetBucketInventoryResult
 * @package OSS\Result
 */
class GetBucketInventoryResult extends Result
{
    /**
     *  Parse the InventoryInfo object from the response
     *
     * @return InventoryInfo []
     */
    protected function parseDataFromResponse()
    {
        $xml = new \SimpleXMLElement($this->rawResponse->body);
        $id = isset($xml->Id) ? strval($xml->Id) : "";
        $isEnabled = isset($xml->IsEnabled) ? strval($xml->IsEnabled) : "";
        $destination = isset($xml->Destination) ? json_decode(json_encode($xml->Destination,LIBXML_NOCDATA),true) : "";
        $schedule = isset($xml->Schedule) ? json_decode(json_encode($xml->Schedule,LIBXML_NOCDATA),true) : "";
        $filter = isset($xml->Filter) ? json_decode(json_encode($xml->Filter,LIBXML_NOCDATA),true) : "";
        $includedObjectVersions = isset($xml->IncludedObjectVersions) ? strval($xml->IncludedObjectVersions) : "";
        $optionalFields = isset($xml->OptionalFields) ? json_decode(json_encode($xml->OptionalFields,LIBXML_NOCDATA),true) : "";
        return new InventoryInfo($id, $isEnabled, $destination,$schedule,$filter,$includedObjectVersions,$optionalFields);
    }
}
