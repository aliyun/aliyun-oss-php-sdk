<?php
namespace OSS\Model;

use OSS\Model\RegionInfo;
/**
 * Class RegionInfoList
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/345596.html
 */
class RegionInfoList {


    /**
     * @var RegionInfo[]
     */
    private $regionInfoList;

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->RegionInfo)) return;
        foreach ($xml->RegionInfo as $regionInfo) {
            $region = strval($regionInfo->Region);
            $internetEndpoint = strval($regionInfo->InternetEndpoint);
            $internalEndpoint = strval($regionInfo->InternalEndpoint);
            $accelerateEndpoint = strval($regionInfo->AccelerateEndpoint);
            $this->regionInfoList[] = new RegionInfo($region, $internetEndpoint, $internalEndpoint, $accelerateEndpoint);
        }
    }

    /**
     * @return RegionInfo[]
     */
    public function getRegionInfoList() {
        return $this->regionInfoList;
    }
}
