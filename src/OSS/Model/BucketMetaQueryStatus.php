<?php

namespace OSS\Model;


/**
 * Bucket stat class.
 *
 * Class BucketMetaQueryStatus
 * @package OSS\Model
 */
class BucketMetaQueryStatus
{
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get phase
     *
     * @return string
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * Get create time
     *
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * Parse stat from the xml.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (isset($xml->State) ) {
            $this->state = strval($xml->State);
        }
        if (isset($xml->Phase) ) {
            $this->phase = strval($xml->Phase);
        }
        if (isset($xml->CreateTime) ) {
            $this->createTime = strval($xml->CreateTime);
        }

        if (isset($xml->UpdateTime) ) {
            $this->updateTime = strval($xml->UpdateTime);
        }

    }

    /**
     * state
     * @var string
     */
    private $state;

    /**
     * phase
     * @var string
     */
    private $phase;

    /**
     * Create time
     * @var string
     */
    private $createTime;

    /**
     * Update time
     * @var string
     */
    private $updateTime;

}