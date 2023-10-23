<?php
namespace OSS;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

/**
 * Class KmsClient
 * @package Oss
 */
class KmsClient{
	
	/**
	 * @var string
	 */
	protected $endpoint;
	/**
	 * @var string
	 */
	protected $accessKeySecret;
	/**
	 * @var string
	 */
	protected $accessKeyId;
	
	protected $version = '2016-01-20';
	
	
	public function __construct($accessKeyId, $accessKeySecret, $endpoint)
	{
		$this->accessKeyId     = $accessKeyId;
		$this->accessKeySecret = $accessKeySecret;
		$this->endpoint        = $endpoint;
		$this->uri             = new Uri();
	}
	
	/**
	 * @param array $request
	 * @param array $runtime
	 * @return string
	 * @throws GuzzleException
	 */
	protected function request(array $request, array $runtime)
	{
		$options = [
			'verify' => isset($runtime['ignoreSSL']) ? (boolean)$runtime['ignoreSSL'] : false,
			'http_errors' => isset($runtime['http_errors']) ? (boolean)$runtime['http_errors'] : false,
		];
		
		$options = array_merge_recursive([$options, $runtime]);
		$this->uri = $this->uri->withScheme($request['protocol']);
		$this->uri = $this->uri->withPath($request['pathname']);
		$this->uri = $this->uri->withQuery($request['query']);
		$this->uri = $this->uri->withHost($request['headers']['host']);
		try {
			$result =  (new Client())->request($request['method'], (string)$this->uri, $options);
		} catch (ClientException $e) {
			$result = $e->getResponse();
		}
		return $result->getBody()->getContents();
		
	}
	
	/**
	 * @param array $query
	 *
	 * @param array $request
	 *
	 * @return string
	 */
	protected function getQuery(array $query, array $request)
	{
		$query['Format'] = 'json';
		$query['Version'] = $this->version;
		$query['AccessKeyId'] = $this->accessKeyId;
		$query['SignatureMethod'] = $this->getMethod();
		$query['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		$query['SignatureVersion'] = $this->getVersion();
		$query['SignatureNonce'] = md5(uniqid(mt_rand(), true));
		$query['Signature'] = $this->sign(
			$this->prepareStringToSigned($query, $request),
			$this->accessKeySecret . '&'
		);
		
		return http_build_query($query);
	}
	
	/**
	 * @param $query
	 * @param $request
	 *
	 * @return string
	 */
	protected function prepareStringToSigned($query, $request)
	{
		ksort($query, SORT_STRING);
		$canonicalizedQuery = '';
		foreach ($query as $key => $value) {
			$canonicalizedQuery .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
		}
		
		return $request['method']
			. '&%2F&'
			. $this->percentEncode(substr($canonicalizedQuery, 1));
	}
	
	/**
	 * @param string $string
	 *
	 * @return null|string|string[]
	 */
	protected function percentEncode($string)
	{
		$result = urlencode($string);
		$result = str_replace(['+', '*'], ['%20', '%2A'], $result);
		$result = preg_replace('/%7E/', '~', $result);
		
		return $result;
	}
	
	
	/**
	 * @description cancel key deletion which can enable this key
	 * @see         https://help.aliyun.com/document_detail/44197.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 * @param $runtime
	 *
	 * @return string json
	 */
	public function cancelKeyDeletion($query = [], $runtime = [])
	{
		$query['Action']     = 'CancelKeyDeletion';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description make alias to key
	 * @see         https://help.aliyun.com/document_detail/68624.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 *   - AliasName string required: cmk alias, prefix must be 'alias/'
	 * @param $runtime
	 *
	 * @return string json
	 */
	public function createAlias($query = [], $runtime = [])
	{
		$query['Action']     = 'CreateAlias';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description create new key
	 * @see         https://help.aliyun.com/document_detail/28947.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - Origin string optional: Aliyun_KMS (default) or EXTERNAL
	 *   - Description string optional: description of key
	 *   - KeyUsage string optional: usage of key, default is ENCRYPT/DECRYPT
	 * @param $runtime
	 *
	 * @return string json
	 */
	public function createKey($query = [], $runtime = [])
	{
		$query['Action']     = 'CreateKey';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description decrypt body of CiphertextBlob
	 * @see         https://help.aliyun.com/document_detail/28950.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - CiphertextBlob string required: ciphertext to be decrypted.
	 *   - EncryptionContext string optional: key/value string, must be {string: string}
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function decrypt($query = [], $runtime = [])
	{
		$query['Action']     = 'Decrypt';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description delete alias
	 * @see         https://help.aliyun.com/document_detail/68626.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - AliasName string required: alias name, prefix must be 'alias/'
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function deleteAlias($query = [], $runtime = [])
	{
		$query['Action']     = 'DeleteAlias';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description delete key material
	 * @see         https://help.aliyun.com/document_detail/68623.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function deleteKeyMaterial($query = [], $runtime = [])
	{
		$query['Action']     = 'DeleteKeyMaterial';
		$request['protocol'] = 'http';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description get description of main key
	 * @see         https://help.aliyun.com/document_detail/28952.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function describeKey($query = [], $runtime = [])
	{
		$query['Action']     = 'DescribeKey';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description query available regions
	 * @see         https://help.aliyun.com/document_detail/54560.html
	 *
	 * @param $query
	 *   - Action string required
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function describeRegions($query = [], $runtime = [])
	{
		$query['Action']     = 'DescribeRegions';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description disable key
	 * @see         https://help.aliyun.com/document_detail/35151.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function disableKey($query = [], $runtime = [])
	{
		$query['Action']     = 'DisableKey';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description enable key
	 * @see         https://help.aliyun.com/document_detail/35150.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function enableKey($query = [], $runtime = [])
	{
		$query['Action']     = 'EnableKey';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description encrypt content
	 * @see         https://help.aliyun.com/document_detail/28949.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 *   - Plaintext string required: plaintext to be encrypted (must be Base64 encoded)
	 *   - EncryptionContext string optional: key/value string, must be {string: string}
	 * @param $runtime
	 *
	 * @return string json
	 * @throws GuzzleException
	 */
	public function encrypt($query = [], $runtime = [])
	{
		$query['Action']     = 'Encrypt';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	
	/**
	 * @description generate local data key
	 * @see         https://help.aliyun.com/document_detail/28948.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 *   - KeySpec string optional: AES_256 or AES_128
	 *   - NumberOfBytes int optional: length of key
	 *   - EncryptionContext string optional: key/value string, must be {string: string}
	 * @param $runtime
	 *
	 * @return string json
	 */
	public function generateDataKey($query = [], $runtime = [])
	{
		$query['Action']     = 'GenerateDataKey';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		return $this->json($response);
	}
	
	/**
	 * @description get the imported master key (CMK) material
	 * @see         https://help.aliyun.com/document_detail/68621.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 *   - WrappingAlgorithm string required: algorithm for encrypting key material, RSAES_PKCS1_V1_5, RSAES_OAEP_SHA_1
	 *   or RSAES_OAEP_SHA_256
	 *   - WrappingKeySpec string required: public key type used to encrypt key material, RSA_2048
	 * @param $runtime
	 *
	 * @return string json
	 */
	public function getParametersForImport($query = [], $runtime = [])
	{
		$query['Action']     = 'GetParametersForImport';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description
	 * @see https://help.aliyun.com/document_detail/68622.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 *   - EncryptedKeyMaterial string required: key material encrypted with base64
	 *   - ImportToken string required: obtained by calling GetParametersForImport
	 *   - KeyMaterialExpireUnix {timestamp} optional: Key material expiration time
	 * @param $runtime
	 *
	 * @return string  json
	 */
	public function importKeyMaterial($query = [], $runtime = [])
	{
		$query['Action']     = 'ImportKeyMaterial';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description returns all aliases of the current user in the current zone
	 * @see         https://help.aliyun.com/document_detail/68627.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - PageNumber int optional: current page, default 1
	 *   - PageSize int optional: result count (0 - 100), default 10
	 * @param $runtime
	 *
	 * @return json string
	 */
	public function listAliases($query = [], $runtime = [])
	{
		$query['Action']     = 'ListAliases';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description list all aliases corresponding to the specified master key (CMK)
	 * @see         https://help.aliyun.com/document_detail/68628.html
	 *
	 * @param $query
	 *   - Action string required
	 *   - KeyId string required: global unique identifier
	 *   - PageNumber int optional: current page, default 1
	 *   - PageSize int optional: result count (0 - 100), default 10
	 * @param $runtime
	 *
	 * @return json string
	 */
	public function listAliasesByKeyId($query = [], $runtime = [])
	{
		$query['Action']     = 'ListAliasesByKeyId';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	

	/**
	 * @description Returns all the master key IDs of the caller in the calling area
	 * @see         https://help.aliyun.com/document_detail/28951.html
	 * @param array $query
	 * @param array $runtime
	 * @return string json
	 */
	public function listKeys($query = [], $runtime = [])
	{
		$query['Action']     = 'ListKeys';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description request to delete a specified master key (CMK)
	 * @see         https://help.aliyun.com/document_detail/44196.html
	 * @param array $query
	 * @param array $runtime
	 * @return json string
	 */
	public function scheduleKeyDeletion($query = [], $runtime = [])
	{
		$query['Action']     = 'ScheduleKeyDeletion';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		
		$response = $this->request($request, $runtime);
		
		return $this->json($response);
	}
	
	/**
	 * @description update the master key (CMK) represented by an existing alias
	 * @see         https://help.aliyun.com/document_detail/68625.html
	 * @param array $query
	 * @param array $runtime
	 * @return json string
	 */
	public function updateAlias($query = [], $runtime = [])
	{
		$query['Action']     = 'UpdateAlias';
		$request['protocol'] = 'https';
		$request['method']   = 'GET';
		$request['pathname'] = '/';
		$request['query']    = $this->getQuery($query, $request);
		$request['headers']  = [
			'host' => $this->endpoint,
		];
		$response = $this->request($request, $runtime);
		return $this->json($response);
	}
	
	/**
	 * @param $response json
	 * @return mixed array()
	 */
	public function json($response){
		return $result = json_decode($response,true);
	}
	
	
	/**
	 * @return string
	 */
	public function getMethod()
	{
		return 'HMAC-SHA1';
	}
	
	/**
	 * @return string
	 */
	public function getType()
	{
		return '';
	}
	
	/**
	 * @return string
	 */
	public function getVersion()
	{
		return '1.0';
	}
	
	/**
	 * @param string $string
	 * @param string $accessKeySecret
	 *
	 * @return string
	 */
	public function sign($string, $accessKeySecret)
	{
		return base64_encode(hash_hmac('sha1', $string, $accessKeySecret, true));
	}
	
	
}