<?php

namespace OSS\Model;

/**
 * Class ReplicationEncryptionConfiguration
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/181408.htm
 */
class ReplicationEncryptionConfiguration
{
    /**
     * @var string
     */
    private $replicaKmsKeyID;

    /**
     * @param $kmsId string
     */
    public function setReplicaKmsKeyID($kmsId)
    {
        $this->replicaKmsKeyID = $kmsId;
    }


    /**
     * @return string
     */
    public function getReplicaKmsKeyID(){
        return  $this->replicaKmsKeyID;
    }

}


