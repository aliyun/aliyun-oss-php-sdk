<?php

namespace OSS\Crypto;

use OSS\Core\OssException;

/**
 * Class RsaEncryptionMaterials
 * @package OSS\Crypto
 */
class RsaEncryptionMaterials {

    private $desc = array();

    private $keyPair = array();

    /**
     * EncryptionMaterials constructor.
     * @param $desc
     * @param $keyPair
     * @throws OssException
     */
    public function __construct($desc, $keyPair)
    {
        if(!is_array($desc)){
            throw new OssException('Invalid type, the type of desc must be array!');
        }
        if(empty($keyPair)){
            throw new OssException('Key pair is not null!');
        }
        if(!is_array($keyPair)){
            throw new OssException('Invalid type, the type of key pair must be array!');
        }
        $this->keyPair = $keyPair;
        $this->desc = $desc;
    }


    /**
     * @return array
     */
    public function getKeyPair(){
        return $this->keyPair;
    }


    /**
     * @return array
     */
    public function getDesc(){
        return $this->desc;
    }
}
