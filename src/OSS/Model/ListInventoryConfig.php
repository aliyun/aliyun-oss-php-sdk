<?php

namespace OSS\Model;

/**
 * Class ListInventoryConfig
 *
 * The class of return value of ListObjects
 *
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177802.html
 */
class ListInventoryConfig
{

    /**
     * @var string
     */
    private $isTruncated;
    /**
     * @var string
     */
    private $nextContinuationToken;

    /**
     * @var InventoryConfig[]
     */
    private $inventoryList;

    /**
     * @return string
     */
    public function getIsTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * get nextContinuationToken
     * @return string
     */
    public function getNextContinuationToken()
    {
        return $this->nextContinuationToken;
    }


    /**
     * get the inventoryInfo list
     * @return InventoryConfig[]
     */
    public function getInventoryList()
    {
        return $this->inventoryList;
    }


    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->IsTruncated) && !isset($xml->NextContinuationToken) && !isset($xml->InventoryConfiguration) && !isset($xml->Schedule)&& !isset($xml->Filter)&& !isset($xml->IncludedObjectVersions)&& !isset($xml->OptionalFields)) return;
        if (isset($xml->IsTruncated)){
            $this->isTruncated = strval($xml->IsTruncated);
        }
        if (isset($xml->NextContinuationToken)){
            $this->nextContinuationToken = strval($xml->NextContinuationToken);
        }
        if (isset($xml->InventoryConfiguration)){
            $this->parseInventoryConfig($xml->InventoryConfiguration);
        }
    }


    /**
     * @param $xmlInventoryConfiguration
     */
    private function parseInventoryConfig($xmlInventoryConfiguration){
        if ($xmlInventoryConfiguration){
            foreach ($xmlInventoryConfiguration as $config){
                $inventoryConfiguration = new InventoryConfig();
                $inventoryConfiguration->parseFromXmlObj($config);
                $this->inventoryList[] = $inventoryConfiguration;
            }
        }
    }

}