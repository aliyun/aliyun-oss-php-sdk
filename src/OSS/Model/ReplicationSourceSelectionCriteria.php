<?php

namespace OSS\Model;

/**
 * Class ReplicationSourceSelectionCriteria
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/181408.htm
 */
class ReplicationSourceSelectionCriteria
{
    private $status;

    /**
     * @param string $status Enabled|Disabled
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string Enabled|Disabled
     */
    public function getStatus(){
        return $this->status;
    }
}


