<?php
namespace OSS\Model;

/**
 * Class AccessPointInfo
 * @package OSS\Model
 *
 */
class AccessPointInfo{

    private $accessPointName;
    private $bucket;
    private $accountId;
    private $networkOrigin;
    private $vpcId;
    private $accessPointArn;
    private $creationDate;
    private $alias;
    private $status;
    private $publicEndpoint;
    private $internalEndpoint;

    public function getAccessPointName(){
        return $this->accessPointName;
    }

    public function getBucket(){
        return $this->bucket;
    }

    public function getAccountId(){
        return $this->accountId;
    }

    public function getNetworkOrigin(){
        return $this->networkOrigin;
    }

    public function getVpcId(){
        return $this->vpcId;
    }

    public function getAccessPointArn(){
        return $this->accessPointArn;
    }

    public function getCreationDate() {
        return $this->creationDate;
    }

    public function getAlias(){
        return $this->alias;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getPublicEndpoint() {
        return $this->publicEndpoint;
    }

    public function getInternalEndpoint() {
        return $this->internalEndpoint;
    }

    /**
     * Parse the xml into this object.
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        $this->parseFromXmlObj($xml);
    }


    /**
     * @param $xml \SimpleXMLElement
     */
    public function parseFromXmlObj($xml)
    {
        if (isset($xml->AccessPointName)){
            $this->accessPointName = strval($xml->AccessPointName);
        }
        if (isset($xml->Bucket)){
            $this->bucket = strval($xml->Bucket);
        }
        if (isset($xml->AccountId)){
            $this->accountId = strval($xml->AccountId);
        }
        if (isset($xml->NetworkOrigin)){
            $this->networkOrigin = strval($xml->NetworkOrigin);
        }
        if (isset($xml->VpcConfiguration->VpcId)){
            $this->vpcId = strval($xml->VpcConfiguration->VpcId);
        }
        if (isset($xml->AccessPointArn)){
            $this->accessPointArn = strval($xml->AccessPointArn);
        }
        if (isset($xml->CreationDate)){
            $this->creationDate = strval($xml->CreationDate);
        }
        if (isset($xml->CreationDate)){
            $this->creationDate = strval($xml->CreationDate);
        }
        if (isset($xml->Alias)){
            $this->alias = strval($xml->Alias);
        }
        if (isset($xml->Status)){
            $this->status = strval($xml->Status);
        }

        if (isset($xml->Endpoints->PublicEndpoint)){
            $this->publicEndpoint =  strval($xml->Endpoints->PublicEndpoint);
        }

        if (isset($xml->Endpoints->InternalEndpoint)){
            $this->internalEndpoint =  strval($xml->Endpoints->InternalEndpoint);
        }
    }
}