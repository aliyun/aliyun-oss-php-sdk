<?php

namespace OSS\Model;

/**
 * Class ListReservedCapacity
 * @package OSS\Model
 */
class ListReservedCapacity
{
    /**
     * @var ReservedCapacityRecord[]
     */
    private $list;

    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->ReservedCapacityRecord)) return;
        if (isset($xml->ReservedCapacityRecord)){
            $this->parseReservedCapacity($xml->ReservedCapacityRecord);
        }
    }

    /**
     * @param $xmlReservedCapacityRecord
     */
    private function parseReservedCapacity($xmlReservedCapacityRecord){
        if ($xmlReservedCapacityRecord){
            foreach ($xmlReservedCapacityRecord as $record){
                $reservedCapacity = new ReservedCapacityRecord();
                $reservedCapacity->parseFromXmlObj($record);
                $this->list[] = $reservedCapacity;
            }
        }
    }

    /**
     * @return ReservedCapacityRecord[]
     */
    public function getReservedCapacityList(){
        return $this->list;
    }
}