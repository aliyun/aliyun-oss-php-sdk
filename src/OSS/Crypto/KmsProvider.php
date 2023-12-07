<?php
namespace Oss\Crypto;

use GuzzleHttp\Exception\GuzzleException;
use OSS\Core\OssException;
use OSS\Crypto\Cipher\AesCtrCipher;
use Oss\KmsClient;
use OSS\Model\ContentCryptoMaterial;

/**
 * Uses KMS to supply materials for encrypting and decrypting data
 * Class KmsMaterialsProvider
 * @package Oss\Crypto
 */
class KmsProvider extends BaseCryptoProvider
{

    private $kmsClient;
    private $customerKeyId;
    private $wrapAlg;

    private $accessKeyId;
    private $accessKeySecret;

    /**
     * KmsProvider constructor.
     * @param $accessKeyId
     * @param $accessKeySecret
     * @param $region
     * @param $cmkId
     * @param null $mat_desc
     * @param string $cipherAdaptor
     * @throws \Exception
     */
    public function __construct($accessKeyId,$accessKeySecret,$region,$cmkId,$mat_desc=null,$cipherAdaptor="AesCtrCipher") {
        parent::__construct($cipherAdaptor,$mat_desc);
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->kmsClient = new KmsClient($accessKeyId,$accessKeySecret,$region);
        $this->customerKeyId = $cmkId;
        $this->wrapAlg = Crypto::KMS_ALI_WRAP_ALGORITHM;
    }

    /**
     * get a random key
     * @return array
     */
    public function getKey()
    {
        list($key,$encryptedKey) = $this->generateData(32);
        return array($key,$encryptedKey);
    }

    /**
     * get a random key
     * @return array
     */
    public function getIv()
    {
        list($iv,$encryptedIv) = $this->generateData(16);
        return array($iv,$encryptedIv);
    }

    /**
     * @return ContentCryptoMaterial
     * @throws GuzzleException|OssException
     */
    public function createContentMaterial(){
        list($key,$encryptedKey) = $this->getKey();
        list($iv,$encryptedIv) = $this->getIv();
        $wrapAlg = $this->wrapAlg;
        $matDesc = $this->matDesc;
        $this->cipher->init(base64_decode($key),base64_decode($iv));
        return new ContentCryptoMaterial($this->cipher,$wrapAlg,$encryptedKey,$encryptedIv,$matDesc);
    }

    /**
     * @param KmsEncryptionMaterials $encryptionMaterials
     * @return KmsProvider
     */
    public function resetEncryptionMaterials($encryptionMaterials)
    {
        $provider = $this;
        $this->kmsClient = new KmsClient($this->accessKeyId,$this->accessKeySecret,$encryptionMaterials->getKmsRegion());
        $provider->matDesc = $encryptionMaterials->getDesc();
        $this->customerKeyId = $encryptionMaterials->getKmsId();
        return $provider;
    }

    /**
     * Assemble encrypted information
     * @return void
     */
    public function addEncryptionMaterials($encryptionMaterials){
        parent::addEncryptionMaterials($encryptionMaterials);
    }

    /**
     * Assemble encrypted information
     * @return KmsEncryptionMaterials
     */
    public function getEncryptionMaterials($desc){
        return parent::getEncryptionMaterials($desc);
    }


    /**
     * @param $content
     * @param AesCtrCipher $cipher
     * @return int|string
     * @throws OssException
     */
    public function encryptAdapter($content, $cipher)
    {
        return $cipher->encrypt($content,$cipher);
    }

    /**
     * @param $content
     * @param $cipher AesCtrCipher
     * @return int|string
     * @throws OssException
     */
    public function decryptAdapter($content, $cipher)
    {
        return $cipher->decrypt($content,$cipher);
    }


    /**
     * @param string $encryptedKey
     * @return false|string
     * @throws GuzzleException
     */
    public function decryptKey($encryptedKey)
    {
        return base64_decode((string)$this->decryptData($encryptedKey));
    }

    /**
     * @param $encryptedIv
     * @return false|string
     * @throws GuzzleException
     */
    public function decryptIv($encryptedIv)
    {
        return base64_decode((string)$this->decryptData($encryptedIv));
    }

    /**
     * @return array
     */
    public function generateData($len)
    {
        $result = $this->kmsClient->generateDataKey([
            'KeyId' => $this->customerKeyId,
            'KeySpec' => "AES_256",
            "NumberOfBytes" =>$len,
        ]);

        return array(
            $result['Plaintext'],
            $result['CiphertextBlob'],
        );
    }


    /**
     * @return array
     * @throws GuzzleException
     */
    public function decryptData($data)
    {
        $result = $this->kmsClient->decrypt([
            'CiphertextBlob' =>$data,
        ]);

        return $result['Plaintext'];
    }

    public function getMatDesc() {
        return $this->matDesc;
    }

    public function getCipher() {
        return $this->cipher;
    }

    public function getWrapAlg() {
        return $this->wrapAlg;
    }
}
