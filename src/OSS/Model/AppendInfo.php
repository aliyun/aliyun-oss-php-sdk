<?php

namespace OSS\Model;

/**
 * Class AppendInfo
 * @package OSS\Model
 */
class AppendInfo
{
    /**
     * AppendInfo constructor.
     * @param int $position
     * @param int $crc
     */
    public function __construct($position, $crc)
    {
        $this->position = $position;
        $this->crc = $crc;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getCrc()
    {
        return $this->crc;
    }

    private $position;
    private $crc;
}