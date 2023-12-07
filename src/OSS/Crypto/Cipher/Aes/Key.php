<?php

namespace OSS\Crypto\Cipher\Aes;

use OSS\Core\OssException;

/**
 * Class Key
 * @package OSS\Crypto\Cipher\Aes
 */
class Key
{
    private $bits;
    private $encryptionKey;
    private $decryptionKey;

    /**
     * @throws OssException
     */
    public function __construct($key)
    {
        $bits = strlen($key) << 3;
        switch ($bits) {
            case 128:
                $this->generate128($key);
                break;
            case 192:
                $this->generate192($key);
                break;
            case 256:
                $this->generate256($key);
                break;
            default:
                throw new OssException("Invalid key, the length of key must be 32!");
        }

        $this->bits = $bits;
    }

    /**
     * @return int
     */
    public function bits()
    {
        return $this->bits;
    }

    /**
     * @return array
     */
    public function encryptionKey()
    {
        return $this->encryptionKey;
    }


    /**
     * @return mixed
     */
    public function decryptionKey()
    {
        return $this->decryptionKey;
    }


    /**
     * @param int $k
     * @param int $rc
     * @return int
     */
    private function encryptionKeyRound( $k, $rc)
    {
        return (Constants::SUBBYTES[$k       & 0xff]        <<  8) |
            (Constants::SUBBYTES[$k >>  8 & 0xff]        << 16) |
            ((Constants::SUBBYTES[$k >> 16 & 0xff] ^ $rc) << 24) |
            Constants::SUBBYTES[$k >> 24       ];
    }

    /**
     * @param int $k
     * @return int
     */
    private function decryptionKeyRound($k)
    {
        return Constants::MIXCOLUMNS_INVERSE_0[Constants::SUBBYTES[$k >> 24       ]] ^
            Constants::MIXCOLUMNS_INVERSE_1[Constants::SUBBYTES[$k >> 16 & 0xff]] ^
            Constants::MIXCOLUMNS_INVERSE_2[Constants::SUBBYTES[$k >>  8 & 0xff]] ^
            Constants::MIXCOLUMNS_INVERSE_3[Constants::SUBBYTES[$k       & 0xff]];
    }

    /**
     * @param string $key
     */
    private function generate128($key)
    {
        list(, $k0, $k1, $k2, $k3) = unpack('N4', $key);

        $encryptionKey =
        $decryptionKey = [$k0, $k1, $k2, $k3];

        for ($i = 4, $rc = 1; $i < 40; $rc = ($rc << 1) % 0xe5) {
            $encryptionKey[$i  ] = $k0 ^= $this->encryptionKeyRound($k3, $rc);
            $decryptionKey[$i++] = $this->decryptionKeyRound($k0);
            $encryptionKey[$i  ] = $k1 ^= $k0;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k1);
            $encryptionKey[$i  ] = $k2 ^= $k1;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k2);
            $encryptionKey[$i  ] = $k3 ^= $k2;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k3);
        }

        $encryptionKey[56] = $decryptionKey[56] = $k0 ^= $this->encryptionKeyRound($k3, 0x36);
        $encryptionKey[57] = $decryptionKey[57] = $k1 ^= $k0;
        $encryptionKey[58] = $decryptionKey[58] = $k2 ^= $k1;
        $encryptionKey[59] = $decryptionKey[59] = $k3 ^ $k2;

        $this->encryptionKey = $encryptionKey;
        $this->decryptionKey = $decryptionKey;
    }

    /**
     * @param string $key
     */
    private function generate192($key)
    {
        list(, $k0, $k1, $k2, $k3, $k4, $k5) = unpack('N6', $key);

        $encryptionKey = [$k0, $k1, $k2, $k3, $k4, $k5];
        $decryptionKey = [
            $k0, $k1, $k2, $k3,
            $this->decryptionKeyRound($k4),
            $this->decryptionKeyRound($k5)
        ];

        for ($i = 6, $rc = 1; $i < 48; $rc <<= 1) {
            $encryptionKey[$i  ] = $k0 ^= $this->encryptionKeyRound($k5, $rc);
            $decryptionKey[$i++] = $this->decryptionKeyRound($k0);
            $encryptionKey[$i  ] = $k1 ^= $k0;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k1);
            $encryptionKey[$i  ] = $k2 ^= $k1;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k2);
            $encryptionKey[$i  ] = $k3 ^= $k2;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k3);
            $encryptionKey[$i  ] = $k4 ^= $k3;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k4);
            $encryptionKey[$i  ] = $k5 ^= $k4;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k5);
        }

        $encryptionKey[56] = $decryptionKey[56] = $k0 ^= $this->encryptionKeyRound($k5, 0x80);
        $encryptionKey[57] = $decryptionKey[57] = $k1 ^= $k0;
        $encryptionKey[58] = $decryptionKey[58] = $k2 ^= $k1;
        $encryptionKey[59] = $decryptionKey[59] = $k3 ^ $k2;

        $this->encryptionKey = $encryptionKey;
        $this->decryptionKey = $decryptionKey;
    }

    /**
     * @param string $key
     */
    private function generate256($key)
    {
        list(, $k0, $k1, $k2, $k3, $k4, $k5, $k6, $k7) = unpack('N8', $key);

        $encryptionKey = [$k0, $k1, $k2, $k3, $k4, $k5, $k6, $k7];
        $decryptionKey = [
            $k0, $k1, $k2, $k3,
            $this->decryptionKeyRound($k4),
            $this->decryptionKeyRound($k5),
            $this->decryptionKeyRound($k6),
            $this->decryptionKeyRound($k7)
        ];

        for ($i = 8, $rc = 1; $i < 56; $rc <<= 1) {
            $encryptionKey[$i  ] = $k0 ^= $this->encryptionKeyRound($k7, $rc);
            $decryptionKey[$i++] = $this->decryptionKeyRound($k0);
            $encryptionKey[$i  ] = $k1 ^= $k0;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k1);
            $encryptionKey[$i  ] = $k2 ^= $k1;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k2);
            $encryptionKey[$i  ] = $k3 ^= $k2;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k3);
            $encryptionKey[$i  ] = $k4 ^= (Constants::SUBBYTES[$k3 & 0xff] | (Constants::SUBBYTES[$k3 >> 8 & 0xff] << 8) | (Constants::SUBBYTES[$k3 >> 16 & 0xff] << 16) | (Constants::SUBBYTES[$k3 >> 24] << 24));
            $decryptionKey[$i++] = $this->decryptionKeyRound($k4);
            $encryptionKey[$i  ] = $k5 ^= $k4;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k5);
            $encryptionKey[$i  ] = $k6 ^= $k5;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k6);
            $encryptionKey[$i  ] = $k7 ^= $k6;
            $decryptionKey[$i++] = $this->decryptionKeyRound($k7);
        }

        $encryptionKey[56] = $decryptionKey[56] = $k0 ^= $this->encryptionKeyRound($k7, 0x40);
        $encryptionKey[57] = $decryptionKey[57] = $k1 ^= $k0;
        $encryptionKey[58] = $decryptionKey[58] = $k2 ^= $k1;
        $encryptionKey[59] = $decryptionKey[59] = $k3 ^ $k2;

        $this->encryptionKey = $encryptionKey;
        $this->decryptionKey = $decryptionKey;
    }
}
