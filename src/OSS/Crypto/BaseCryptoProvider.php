<?php
namespace OSS\Crypto;
use OSS\Core\OssException;
use OSS\Crypto\Cipher\Aes\AesCipher;
use OSS\Crypto\Cipher\AesCtrCipher;
use OSS\Model\ContentCryptoMaterial;
/**
 * Class BaseCryptoProvider
 * @package OSS\Crypto
 */
abstract class BaseCryptoProvider
{
    /**
     * mat desc
     * @var array
     */
    protected $matDesc;

    /**
     * @var AesCipher|AesCtrCipher
     */
    protected $cipher;

    /**
     * @var string
     */
    protected $cipherAdaptor;

    /**
     * @var array
     */
    protected $encryptionMaterials = array();

    /**
     * BaseCryptoProvider constructor.
     * @param string $cipherAdaptor
     * @param null $matDesc
     * @throws OssException
     */
    public function __construct($cipherAdaptor="AesCtrCipher",$matDesc=null)
    {
        $this->cipherAdaptor = $cipherAdaptor;
        $class = "OSS\\Crypto\\Cipher\\".$cipherAdaptor;
        if (class_exists($class)) {
            $this->cipher = new $class();
        } else {
            throw new OssException('Error: Could not load Cipher adaptor ' . $cipherAdaptor . '!');
        }
        if($matDesc != null){
            if(is_array($matDesc)){
                $this->matDesc = $matDesc;
            }else{
                throw new OssException('Invalid type, the type of mat_desc must be array!');
            }
        }
    }

    /**
     * @param $encryptedKey string
     */
    public function decryptKey($encryptedKey){}

    /**
     * @param $encryptedIv
     */
    public function decryptIv($encryptedIv){}

    /**
     * Assemble encrypted information
     * @param $encryptionMaterials RsaEncryptionMaterials|KmsEncryptionMaterials
     */
    public function addEncryptionMaterials($encryptionMaterials){
        $key = key($encryptionMaterials->getDesc());
        $this->encryptionMaterials[$key] = $encryptionMaterials;
    }


    /**
     * @param $desc
     * @return KmsEncryptionMaterials | RsaEncryptionMaterials
     */
    public function getEncryptionMaterials($desc){
        $key = key($desc);
        if(array_key_exists($key,$this->encryptionMaterials)){
            return $this->encryptionMaterials[$key];
        }
    }

    /**
     * Assemble encrypted information
     * @return ContentCryptoMaterial
     */
    public function createContentMaterial(){}

    /**
     * @param $encryptionMaterials  KmsEncryptionMaterials | RsaEncryptionMaterials
     */
    public function resetEncryptionMaterials($encryptionMaterials){}

}