<?php

namespace OSS\Model;

/**
 * Class ListAccessPoints
 * @package OSS\Model
 *
 */
class ListAccessPoints{

    /**
     * @var string
     */
    private $isTruncated;
    /**
     * @var string
     */
    private $nextContinuationToken;

    /**
     * @var AccessPointInfo[]
     */
    private $accessPoints;

    private $accountId;

    private $maxKeys;

    public function getIsTruncated(){
        return $this->isTruncated;
    }

    public function getNextContinuationToken(){
        return $this->nextContinuationToken;
    }

    public function getAccountId(){
        return $this->accountId;
    }

    public function getAccessPoints(){
        return $this->accessPoints;
    }

    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->IsTruncated) && !isset($xml->NextContinuationToken) && !isset($xml->AccountId) && !isset($xml->MaxKeys) && !isset($xml->AccessPoints)) return;
        if (isset($xml->IsTruncated)){
            $this->isTruncated = strval($xml->IsTruncated) === 'TRUE' || strval($xml->IsTruncated) === 'true';
        }
        if (isset($xml->NextContinuationToken)){
            $this->nextContinuationToken = strval($xml->NextContinuationToken);
        }
        if (isset($xml->AccountId)){
            $this->accountId = strval($xml->AccountId);
        }
        if (isset($xml->AccessPoints)){
            $this->parseAccessPoints($xml->AccessPoints);
        }
    }

    /**
     * @param $xmlAccessPoints \SimpleXMLElement
     */
    private function parseAccessPoints($xmlAccessPoints){
        if ($xmlAccessPoints){
            foreach ($xmlAccessPoints->AccessPoint as $accessPoint){
                $access = new AccessPointInfo();
                $access->parseFromXmlObj($accessPoint);
                $this->accessPoints[] = $access;
            }
        }
    }

}