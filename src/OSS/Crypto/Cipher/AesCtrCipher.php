<?php

namespace OSS\Crypto\Cipher;

use OSS\Core\OssException;
use OSS\Crypto\Cipher\Aes\AesCipher;
use OSS\Crypto\Cipher\Aes\Context\CTR\Context;
use OSS\Crypto\Cipher\Aes\Key;

class AesCtrCipher extends AesCipher
{

    private $cipher;
    /**
     * @var int
     */
    private $context;

    private $offset = 0;


    /**
     * AesCtrCipher constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->alg = self::AES_CTR;
        $this->cipher ='AES-256-CTR';
        $this->context = new Context;
    }

    public function resetContext(){
        $this->context = new Context;
    }


    /**
     * @param $key
     * @param $iv
     * @throws OssException
     */
    public function init($key,$iv){
        parent::init($key,$iv);
        $this->context->key = new Key($key);
        $this->context->nonce = array_values(unpack('N4', $iv));
        if($this->offset > 0){
            $this->incNonce($this->context,$this->offset);
        }
    }


    /**
     * @param Context $context
     * @param string $message
     * @return string
     * @throws OssException
     */
    private function transcrypt($context,$message)
    {
        $nonce = $context->nonce;
        $keyStream = $context->keyStream;
        $bytesRequired = strlen($message) - strlen($keyStream);
        $bytesOver = $bytesRequired % 16;
        $blockCount = ($bytesRequired >> 4) + ($bytesOver > 0);
        while ($blockCount-- > 0) {
            $keyStream .= $this->encryptBlock($context->key, pack('N4', ...$nonce));
            for($i = 3; $i >= 0; $i--) {
                $nonce[$i]++;
                $nonce[$i] &= 0xffffffff;
                if ($nonce[$i]) {
                    break;
                }
            }
        }
        $context->keyStream = substr($keyStream, $bytesRequired);
        $context->nonce = $nonce;
        return $message ^ $keyStream;
    }


    /**
     * @param $offset
     */
    public function calcOffset($offset)
    {
        $this->offset = $offset;
    }


    /**
     * @param Context $context
     * @param string | int $offset
     */
    public function incNonce($context,$offset){
        $nonce = $context->nonce;
        $blockCount = ($offset/16);
        while ($blockCount-- > 0) {
            for($i = 3; $i >= 0; $i--) {
                $nonce[$i]++;
                $nonce[$i] &= 0xffffffff;
                if ($nonce[$i]) {
                    break;
                }
            }
        }
        $context->nonce = $nonce;
    }

    /**
     * @param Context $ctx
     * @param string $message
     * @return string
     * @throws OssException
     */
    function streamEncrypt( $ctx,  $message)
    {
        return $this->transcrypt($ctx, $message);
    }


    /**
     * @param Context $ctx
     * @param string $message
     * @return string
     * @throws OssException
     */
    function streamDecrypt( $ctx,  $message)
    {
        return $this->transcrypt($ctx, $message);
    }


    /**
     * get a random key
     * @return string
     */
    public function getKey()
    {
        return $this->key = openssl_random_pseudo_bytes($this->keyLen);
    }

    /**
     * get a random iv
     * @return string
     */
    public function getIv()
    {
        return $this->iv = openssl_random_pseudo_bytes(16);
    }

    /**
     * @param $data
     * @param AesCipher $cipher
     * @return int|string
     * @throws OssException
     */
    public function encrypt($data,$cipher)
    {
        return $this->streamEncrypt($this->context, $data);
    }

    /**
     * @param $data
     * @param AesCipher $cipher
     * @return int|string
     * @throws OssException
     */
    public function decrypt($data,$cipher)
    {
        return $this->streamDecrypt($this->context, $data);
    }

    public function getCipher() {
        return $this->cipher;
    }
}
