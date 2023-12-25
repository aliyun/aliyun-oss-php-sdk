<?php
namespace OSS\Signer;

use DateTime;
use OSS\Http\RequestCore;
use OSS\Credentials\Credentials;

class SignerV4 implements SignerInterface
{
    public function sign(RequestCore $request, Credentials $credentials, array &$options)
    {
        // Date
        if (!isset($request->request_headers['Date'])) {
            $request->add_header('Date', gmdate('D, d M Y H:i:s \G\M\T'));
        }
        $timestamp = strtotime($request->request_headers['Date']);
        if ($timestamp === false) {
            $timestamp = time();
        }
        $datetime = gmdate('Ymd\THis\Z', $timestamp);
        $date = substr($datetime, 0, 8);
        $request->add_header("x-oss-date", $datetime);

        // Credentials information
        if (!empty($credentials->getSecurityToken())) {
            $request->add_header("x-oss-security-token", $credentials->getSecurityToken());
        }

        $headers = $request->request_headers;
        $method = strtoupper($request->method);

        $region = $options['region'];
        $product = $options['product'];

        $scope = $this->buildScope($date, $region, $product);

        $resourcePath = $this->getResourcePath($options);

        $additionalHeaders = $this->getCommonAdditionalHeaders();

        $queryString = parse_url($request->request_url, PHP_URL_QUERY);
        $query = array();
        parse_str($queryString, $query);

        $canonicalRequest = $this->calcCanonicalRequest($method, $resourcePath, $query, $headers, $additionalHeaders);

        $stringToSign = $this->calcStringToSign($datetime, $scope, $canonicalRequest);

        $options['string_to_sign'] = $stringToSign;

        $signature = $this->calcSignature($credentials->getAccessKeySecret(), $date, $region, $product, $stringToSign);

        $authorization = 'OSS4-HMAC-SHA256 Credential='.$credentials->getAccessKeyId().'/'.$scope;

        $additionalHeadersString = implode(';', $additionalHeaders);

        if ($additionalHeadersString !== ''){
            $authorization .= ',AdditionalHeaders='.$additionalHeadersString;
        }
        $authorization .= ',Signature='.$signature;

        $request->add_header('Authorization', $authorization);
    }

    public function presign(RequestCore $request, Credentials $credentials, array &$options)
    {

    }

    private function getResourcePath(array $options)
    {
        $resourcePath = '/';
        if (!empty($options['bucket'])) {
            $resourcePath .= $options['bucket'].'/';
        }
        if (!empty($options['key'])) {
            $resourcePath .= $options['key'];
        }
        return $resourcePath;
    }

    private function getCommonAdditionalHeaders()
    {
        return array();
    }

    private function isDefaultSignedHeader($low) {
        if (strncmp($low, "x-oss-", 6) == 0 ||
            $low === "content-type" ||
            $low === "content-md5") {
            return true;
        }
        return false;
    }

    private function calcStringToSign($datetime, $scope, $canonicalRequest) {
        /*
        StringToSign
        "OSS4-HMAC-SHA256" + "\n" +
        TimeStamp + "\n" +
        Scope + "\n" +
        Hex(SHA256Hash(Canonical Request))
        */
        $hashedRequest = hash('sha256', $canonicalRequest);
        return "OSS4-HMAC-SHA256"."\n".$datetime."\n".$scope."\n".$hashedRequest;
    }

    private function calcCanonicalRequest($method, $resourcePath, array $query, array $headers, array $additionalHeaders) {
        /*
            Canonical Request
            HTTP Verb + "\n" +
            Canonical URI + "\n" +
            Canonical Query String + "\n" +
            Canonical Headers + "\n" +
            Additional Headers + "\n" +
            Hashed PayLoad
        */
    
        //Canonical Uri
        $canonicalUri = str_replace(array('%2F'), array('/'), rawurlencode($resourcePath));
    
        //Canonical Query
        $querySigned = array();
        foreach ($query as $key => $value) {
            $querySigned[rawurlencode($key)] = rawurlencode($value);
        }
        ksort($querySigned);
        $sortedQueryList = array();
        foreach ($querySigned as $key => $value) {
            if (strlen($value) > 0) {
                $sortedQueryList[] = $key.'='.$value;
            } else {
                $sortedQueryList[] = $key;
            }
        }
        $canonicalQuery = implode('&', $sortedQueryList);

        //Canonical Headers
        $addHeaders = array();
        foreach ($additionalHeaders as $key) {
            $lowk = strtolower($key);
            $$addHeaders[] = $lowk;
        }
        $addHeaders = array();
        ksort($addHeaders);

        $headersSigned = array();
        foreach ($headers as $key => $value) {
            $lowk = strtolower($key);
            if (SignerV4::isDefaultSignedHeader($lowk) ||
                in_array($lowk, $addHeaders)) {
                $headersSigned[$lowk] = trim($value); 
            }
        }
        ksort($headersSigned);
        $canonicalizedHeaders = '';
        foreach ($headersSigned as $key => $value) {
            $canonicalizedHeaders .= $key.':' . $value . "\n";
        }

        //Additional Headers
        $canonicalAdditionalHeaders = implode('&', $addHeaders);
    
        $hashPayload = "UNSIGNED-PAYLOAD";
        if (isset($headersSigned['x-oss-content-sha256'])) {
            $hashPayload = $headersSigned['x-oss-content-sha256'];
        }

        $stringToSign = $method."\n"
            .$canonicalUri."\n"
            .$canonicalQuery."\n"
            .$canonicalizedHeaders."\n"
            .$canonicalAdditionalHeaders."\n"
            .$hashPayload;

        return $stringToSign;
    }

    private function buildScope($date, $region, $product) {
        return $date."/".$region."/".$product."/aliyun_v4_request";
    }

    private function calcSignature($secret, $date, $region, $product, $stringToSign) {
        $h1Key = hash_hmac("sha256", $date, "aliyun_v4" . $secret, true);
        $h2Key = hash_hmac("sha256", $region, $h1Key, true);
        $h3Key = hash_hmac("sha256", $product, $h2Key, true);
        $h4Key = hash_hmac("sha256", "aliyun_v4_request", $h3Key, true);
        return bin2hex(hash_hmac("sha256", $stringToSign, $h4Key, true));
    }
}
