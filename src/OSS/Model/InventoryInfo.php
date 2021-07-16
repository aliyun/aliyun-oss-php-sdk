<?php

namespace OSS\Model;

/**
 * Class PartInfo
 * @package OSS\Model
 */
class InventoryInfo
{

    private $id = "";
    private $isEnabled = "";
    private $destination = array();
    private $schedule = array();
    private $filter = '';
    private $includedObjectVersions = array();
    private $optionalFields = array();
    /**
     * PartInfo constructor.
     *
     * @param int $partNumber
     * @param string $lastModified
     * @param string $eTag
     * @param int $size
     */
    public function __construct($id, $isEnabled, $destination,$schedule,$filter,$includedObjectVersions,$optionalFields)
    {
        $this->id = $id;
        $this->isEnabled = $isEnabled;
        $this->destination = $destination;
        $this->schedule = $schedule;
        $this->filter = $filter;
        $this->includedObjectVersions = $includedObjectVersions;
        $this->optionalFields = $optionalFields;
    }
}