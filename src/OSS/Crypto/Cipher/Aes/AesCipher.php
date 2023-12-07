<?php

namespace OSS\Crypto\Cipher\Aes;
use OSS\Core\OssException;

/**
 * Class AesCipher
 * @package OSS\Crypto\Cipher\Aes
 */
abstract class AesCipher
{

    const AES_256_KEY_SIZE = 32;
    const AES_BLOCK_LEN = 16;
    const AES_BLOCK_BITS_LEN = 8 * 16;
    const AES_GCM = 'AES/GCM/NoPadding';
    const AES_CTR = 'AES/CTR/NoPadding';

    protected $alg;
    protected $keyLen;
    protected $blockSizeLen;
    protected $blockSizeLenInBits;
    protected $key;
    protected $iv;

    public function __construct()
    {
        $this->alg = null;
        $this->keyLen = self::AES_256_KEY_SIZE;
        $this->blockSizeLen = self::AES_BLOCK_LEN;
        $this->blockSizeLenInBits = self::AES_BLOCK_BITS_LEN;
    }

    public function init($key,$iv){
        $this->key = $key;
        $this->iv = $iv;
    }

    public function resetContext(){
    }

    /**
     * @param int $a
     * @param int $b
     * @param int $c
     * @param int $d
     * @return int
     */
    private function mixColumns($a,$b,$c,$d)
    {
        return Constants::MIXCOLUMNS_0[$a >> 24] ^
            Constants::MIXCOLUMNS_1[$b >> 16 & 0xff] ^
            Constants::MIXCOLUMNS_2[$c >> 8 & 0xff] ^
            Constants::MIXCOLUMNS_3[$d & 0xff];
    }


    /**
     * @param int $a
     * @param int $b
     * @param int $c
     * @param int $d
     * @return int
     */
    private function mixColumnsInverse($a,$b,$c, $d)
    {
        return Constants::MIXCOLUMNS_INVERSE_0[$a >> 24] ^
            Constants::MIXCOLUMNS_INVERSE_1[$b >> 16 & 0xff] ^
            Constants::MIXCOLUMNS_INVERSE_2[$c >> 8 & 0xff] ^
            Constants::MIXCOLUMNS_INVERSE_3[$d & 0xff];
    }


    /**
     * @param int $a
     * @param int $b
     * @param int $c
     * @param int $d
     * @return int
     */
    private function subBytes($a,$b,$c,$d)
    {
        return (Constants::SUBBYTES[$a >> 24] << 24) |
            (Constants::SUBBYTES[$b >> 16 & 0xff] << 16) |
            (Constants::SUBBYTES[$c >> 8 & 0xff] << 8) |
            Constants::SUBBYTES[$d & 0xff];
    }


    /**
     * @param int $a
     * @param int $b
     * @param int $c
     * @param int $d
     * @return int
     */
    private function subBytesInverse($a,$b,$c,$d)
    {
        return (Constants::SUBBYTES_INVERSE[$a >> 24] << 24) |
            (Constants::SUBBYTES_INVERSE[$b >> 16 & 0xff] << 16) |
            (Constants::SUBBYTES_INVERSE[$c >> 8 & 0xff] << 8) |
            Constants::SUBBYTES_INVERSE[$d & 0xff];
    }


    /**
     * @param Key $key
     * @param string $block
     * @return string
     * @throws OssException
     */
    public function encryptBlock($key,$block)
    {
        if (strlen($block) !== 16) {
            throw new OssException("Invalid block length, the length of block must be 16 ");
        }
        $k = $key->encryptionKey();

        list(, $a, $b, $c, $d) = unpack('N4', $block);

        $a ^= $k[0];
        $b ^= $k[1];
        $c ^= $k[2];
        $d ^= $k[3];

        $i = 4;
        $rounds = ($key->bits() >> 5) + 5;
        while ($rounds--) {
            list($a, $b, $c, $d) = [
                $this->mixColumns($a, $b, $c, $d) ^ $k[$i++],
                $this->mixColumns($b, $c, $d, $a) ^ $k[$i++],
                $this->mixColumns($c, $d, $a, $b) ^ $k[$i++],
                $this->mixColumns($d, $a, $b, $c) ^ $k[$i++]
            ];
        }

        return pack('N4',
            $this->subBytes($a, $b, $c, $d) ^ $k[56],
            $this->subBytes($b, $c, $d, $a) ^ $k[57],
            $this->subBytes($c, $d, $a, $b) ^ $k[58],
            $this->subBytes($d, $a, $b, $c) ^ $k[59]
        );
    }


    /**
     * @param Key $key
     * @param string $block
     * @return string
     * @throws OssException
     */
    protected function decryptBlock($key,$block)
    {
        if (strlen($block) !== 16) {
            throw new OssException("Invalid block length, the length of block must be 16 ");
        }

        $k = $key->decryptionKey();

        list(, $a, $b, $c, $d) = unpack('N4', $block);

        $d ^= $k[59];
        $c ^= $k[58];
        $b ^= $k[57];
        $a ^= $k[56];

        $i = ($key->bits() >> 3) + 23;
        while ($i > 3) {
            list($d, $c, $b, $a) = [
                $this->mixColumnsInverse($d, $c, $b, $a) ^ $k[$i--],
                $this->mixColumnsInverse($c, $b, $a, $d) ^ $k[$i--],
                $this->mixColumnsInverse($b, $a, $d, $c) ^ $k[$i--],
                $this->mixColumnsInverse($a, $d, $c, $b) ^ $k[$i--],
            ];
        }

        return pack('N4',
            $this->subBytesInverse($a, $d, $c, $b) ^ $k[0],
            $this->subBytesInverse($b, $a, $d, $c) ^ $k[1],
            $this->subBytesInverse($c, $b, $a, $d) ^ $k[2],
            $this->subBytesInverse($d, $c, $b, $a) ^ $k[3]
        );
    }


    /**
     * @return int
     */
    public function getAlignLen(){
        return $this->blockSizeLen;
    }


    /**
     * @return int
     */
    public function getKeyLen(){
        return $this->keyLen;
    }

    /**
     * @return string
     */
    public function getAlg(){
        return $this->alg;
    }
}

