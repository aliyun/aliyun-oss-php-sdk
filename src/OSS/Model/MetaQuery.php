<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class MetaQuery
 * @package OSS\Model
 *
 */
class MetaQuery implements XmlConfig
{

    /**
     * Parse RestoreConfig from the xml.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
     */
    public function parseFromXml($strXml)
    {
        throw new OssException("Not implemented.");
    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><MetaQuery></MetaQuery>');
        if (isset($this->nextToken)) {
            $xml->addChild('NextToken',$this->nextToken);
        }
        if (isset($this->maxResults)) {
            $xml->addChild('MaxResults',$this->maxResults);
        }
        if (isset($this->query)) {
            $xml->addChild('Query',$this->query);
        }
        if (isset($this->sort)) {
            $xml->addChild('Sort',$this->sort);
        }
        if (isset($this->order)) {
            $xml->addChild('Order',$this->order);
        }
        if (isset($this->aggregations) && count($this->aggregations) > 0) {
            $aggregations = $xml->addChild('Aggregations');
            foreach ($this->aggregations as $aggregation) {
                $xmlAggregation = $aggregations->addChild('Aggregation');
                $xmlAggregation->addChild('Field', strval($aggregation->getField()));
                $xmlAggregation->addChild('Operation', strval($aggregation->getOperation()));
            }
        }
        return $xml->asXML();
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->serializeToXml();
    }

    /**
     * @param $aggregation MetaQueryAggregation
     */
    public function addAggregation($aggregation)
    {
        $this->aggregations[] = $aggregation;
    }


    /**
     * @param string $nextToken
     */
    public function setNextToken($nextToken)
    {
        $this->nextToken = $nextToken;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @var string
     */
    private $nextToken;
    /**
     * @var int
     */
    private $maxResults;
    /**
     * @var string
     */
    private $query;
    /**
     * @var string
     */
    private $sort;
    /**
     * @var string
     */
    private $order;
    /**
     * @var MetaQueryAggregation[]
     */
    private $aggregations;
}