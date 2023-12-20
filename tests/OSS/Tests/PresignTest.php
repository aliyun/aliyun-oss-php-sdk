<?php

namespace OSS\Tests;

use DateTime;
use OSS\Core\OssException;
use OSS\Credentials\StaticCredentialsProvider;
use OSS\Http\RequestCore;
use OSS\Http\ResponseCore;
use OSS\OssClient;


class PresignTest extends \PHPUnit\Framework\TestCase
{
    protected $ossClient;

    protected $bucket;

    protected $object;

    protected $stsOssClient;

    public function testPresignWithBasic()
    {
        try {
            $timeout = 3600;
            $signedUrl = $this->ossClient->signUrl($this->bucket, $this->object, $timeout, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->ossClient->signUrl($this->bucket, $this->object, $timeout, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $url = '{"callbackUrl":"http://www.aliyun.com":"bucket=${bucket}&object=${object}"}';

            $var =
                '{
        "x:var1":"value1",
        "x:var2":"value2"
    }';
            $options[OssClient::OSS_SUB_RESOURCE] = 'callback=' . base64_encode($url) . '&callback-var=' . base64_encode($var);
            $signedUrl = $this->ossClient->signUrl($this->bucket, $this->object, $timeout, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("callback=" . base64_encode($url), $signedUrl);
            $this->assertContains("callback-var=" . base64_encode($var), $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->ossClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=" . $expires, $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->ossClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=" . $expires, $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $options[OssClient::OSS_SUB_RESOURCE] = 'callback=' . base64_encode($url) . '&callback-var=' . base64_encode($var);
            $signedUrl = $this->ossClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("callback=" . base64_encode($url), $signedUrl);
            $this->assertContains("callback-var=" . base64_encode($var), $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $this->ossClient->setAuthVersion(OssClient::OSS_AUTH_V4);

        try {
            $signedUrl = $this->ossClient->signUrl($this->bucket, $this->object, $timeout, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->ossClient->signUrl($this->bucket, $this->object, $timeout, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->ossClient->signUrl($this->bucket, $this->object, $timeout, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->ossClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->ossClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $options[OssClient::OSS_SUB_RESOURCE] = 'callback=' . base64_encode($url) . '&callback-var=' . base64_encode($var);
            $signedUrl = $this->ossClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("callback=" . base64_encode($url), $signedUrl);
            $this->assertContains("callback-var=" . base64_encode($var), $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    public function testPresignWithStsToken()
    {
        try {
            $timeout = 3600;
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=" . $expires, $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=" . $expires, $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $this->stsOssClient->setAuthVersion(OssClient::OSS_AUTH_V4);

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT");
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    public function testPresignWithQuery()
    {
        $options = array(
            OssClient::OSS_QUERY_STRING => array(
                'x-oss-process' => 'abc',
                'versionId' => 'versionId'
            ),
        );
        try {
            $timeout = 3600;
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "GET", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=", $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=" . $expires, $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $this->assertContains("OSSAccessKeyId=ak", $signedUrl);
            $this->assertContains("Expires=" . $expires, $signedUrl);
            $this->assertContains("Signature=", $signedUrl);
            $this->assertContains("security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        $this->stsOssClient->setAuthVersion(OssClient::OSS_AUTH_V4);

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "GET", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-process=abc", $signedUrl);
            $this->assertContains("versionId=versionId", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    public function testPresignWithAdditionalHeaders()
    {
        $options = array(
            OssClient::OSS_HEADERS => array(
                "Content-Type" => "application/octet-stream",
                "name" => "aliyun",
                "email" => "aliyun@aliyun.com",
            ),
        );

        $timeout = 3600;
        $expires = time() + 3600;
        $this->stsOssClient->setAuthVersion(OssClient::OSS_AUTH_V4);
        $this->stsOssClient->setAdditionalHeaders(array('name', 'email'));
        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "GET", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-additional-headers=email%3Bname", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->signUrl($this->bucket, $this->object, $timeout, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-additional-headers=email%3Bname", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $expires = time() + 3600;
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "GET", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-additional-headers=email%3Bname", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }

        try {
            $signedUrl = $this->stsOssClient->generatePresignedUrl($this->bucket, $this->object, $expires, "PUT", $options);
            $this->assertContains("bucket.oss-cn-hangzhou.aliyuncs.com/key?", $signedUrl);
            $isoTime = gmdate('Ymd\THis\Z');
            $this->assertContains("x-oss-date=" . $isoTime, $signedUrl);
            $this->assertContains("x-oss-expires=" . $timeout, $signedUrl);
            $this->assertContains("x-oss-signature=", $signedUrl);
            $strDay = gmdate('Ymd');
            $credential = sprintf("ak/%s/cn-hangzhou/oss/aliyun_v4_request", $strDay);
            $this->assertContains("x-oss-credential=" . rawurlencode($credential), $signedUrl);
            $this->assertContains("x-oss-signature-version=OSS4-HMAC-SHA256", $signedUrl);
            $this->assertContains("x-oss-security-token=token", $signedUrl);
            $this->assertContains("x-oss-additional-headers=email%3Bname", $signedUrl);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }

    protected function setUp(): void
    {
        $this->bucket = 'bucket';
        $this->object = 'key';
        $ak = 'ak';
        $sk = 'sk';
        $token = 'token';
        $provider = new StaticCredentialsProvider($ak, $sk);
        $config = array(
            'region' => 'cn-hangzhou',
            'endpoint' => 'oss-cn-hangzhou.aliyuncs.com',
            'provider' => $provider,
        );
        $this->ossClient = new OssClient($config);
        $provider = new StaticCredentialsProvider($ak, $sk, $token);
        $config['provider'] = $provider;
        $this->stsOssClient = new OssClient($config);
    }
}
