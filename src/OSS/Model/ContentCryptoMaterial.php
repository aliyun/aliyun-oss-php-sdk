<?php

namespace OSS\Model;
use OSS\Crypto\Cipher\Aes\AesCipher;
use OSS\Crypto\Crypto;
use OSS\OssClient;
use Oss\OssEncryptionClient;

class ContentCryptoMaterial {

	private $cipher;
    private $wrapAlg;
    private $cekAlg;
    private $encryptedKey;
    private $encryptedIv;
    private $matDesc;
	
	/**
	 * ContentCryptoMaterial constructor.
	 * @param $cipher AesCipher
	 * @param $wrapAlg
	 * @param $encryptedKey
	 * @param $encryptedIv
	 * @param $matDesc
	 */
	public function __construct($cipher, $wrapAlg, $encryptedKey=null, $encryptedIv=null, $matDesc=null)
	{
		$this->cipher = $cipher;
		$this->cekAlg = $cipher->getAlg();
		$this->wrapAlg = $wrapAlg;
		$this->encryptedKey = $encryptedKey;
		$this->encryptedIv = $encryptedIv;
		$this->matDesc = $matDesc;
	}
	
	
	/**
	 * Assemble headers data
	 * @param $headers
	 * @return mixed
	 */
	public function addObjectMeta($headers){
		if(is_array($headers)){
			if (array_key_exists(OssClient::OSS_CONTENT_MD5,$headers)){
				$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_UNENCRYPTED_CONTENT_MD5] = $headers[OssClient::OSS_CONTENT_MD5];
				unset($headers[OssClient::OSS_CONTENT_MD5]);
			}
			
			if (array_key_exists(OssClient::OSS_CONTENT_LENGTH,$headers)){
				$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_UNENCRYPTED_CONTENT_LENGTH] = $headers[OssClient::OSS_CONTENT_LENGTH];
				unset($headers[OssClient::OSS_CONTENT_LENGTH]);
			}
			
		}
		$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_KEY] = $this->encryptedKey;
		$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_START] = $this->encryptedIv;
		$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_CEK_ALG] = $this->cekAlg;
		$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_WRAP_ALG] = $this->wrapAlg;
		
		if($this->matDesc){
			$headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_MATDESC] = json_encode($this->matDesc);
		}
		
		return $headers;
	}
	
	/**
	 * Resolve headers data
	 * @param $headers
	 */
	public function fromObjectMeta($headers){
		$this->encryptedKey = $headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_KEY];
		$this->encryptedIv = $headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_START];
		$this->cekAlg = $headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_CEK_ALG];
		$this->wrapAlg = $headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_WRAP_ALG];
		
		$this->matDesc = json_decode($headers[OssEncryptionClient::X_OSS_META_CLIENT_SIDE_ENCRYPTION_MATDESC],true);
		if($this->wrapAlg == 'kms' || $this->wrapAlg == Crypto::KMS_ALI_WRAP_ALGORITHM){
			$this->wrapAlg = Crypto::KMS_ALI_WRAP_ALGORITHM;
		}else{
			$this->encryptedKey = base64_decode($this->encryptedKey);
			$this->encryptedIv = base64_decode($this->encryptedIv);
		}
	}
	
	/**
	 * @return bool
	 */
	public function isUnencrypted(){
		if($this->encryptedKey == null && $this->encryptedIv == null && $this->cekAlg == null && $this->wrapAlg){
			return true;
		}else{
			return false;
		}
	}

    public function getCipher(){
        return $this->cipher;
    }

    public function getEncryptedIv(){
        return $this->encryptedIv;
    }

    public function getEncryptedKey(){
        return $this->encryptedKey;
    }

    public function getWrapAlg(){
        return $this->wrapAlg;
    }

    public function getCekAlg(){
        return $this->cekAlg;
    }

    public function getMatDesc(){
        return $this->matDesc;
    }
	
}
