<?php
namespace OBS;

use OBS\Core\MimeTypes;
use OBS\Core\ObsException;
use OBS\Http\RequestCore;
use OBS\Http\RequestCore_Exception;
use OBS\Http\ResponseCore;
use OBS\Model\CorsConfig;
use OBS\Model\CnameConfig;
use OBS\Model\LoggingConfig;
use OBS\Model\LiveChannelConfig;
use OBS\Model\LiveChannelInfo;
use OBS\Model\LiveChannelListInfo;
use OBS\Model\StorageCapacityConfig;
use OBS\Result\AclResult;
use OBS\Result\BodyResult;
use OBS\Result\GetCorsResult;
use OBS\Result\GetLifecycleResult;
use OBS\Result\GetLocationResult;
use OBS\Result\GetLoggingResult;
use OBS\Result\GetRefererResult;
use OBS\Result\GetStorageCapacityResult;
use OBS\Result\GetWebsiteResult;
use OBS\Result\GetCnameResult;
use OBS\Result\HeaderResult;
use OBS\Result\InitiateMultipartUploadResult;
use OBS\Result\ListBucketsResult;
use OBS\Result\ListMultipartUploadResult;
use OBS\Model\ListMultipartUploadInfo;
use OBS\Result\ListObjectsResult;
use OBS\Result\ListPartsResult;
use OBS\Result\PutSetDeleteResult;
use OBS\Result\DeleteObjectsResult;
use OBS\Result\CopyObjectResult;
use OBS\Result\CallbackResult;
use OBS\Result\ExistResult;
use OBS\Result\PutLiveChannelResult;
use OBS\Result\GetLiveChannelHistoryResult;
use OBS\Result\GetLiveChannelInfoResult;
use OBS\Result\GetLiveChannelStatusResult;
use OBS\Result\ListLiveChannelResult;
use OBS\Result\AppendResult;
use OBS\Model\ObjectListInfo;
use OBS\Result\SymlinkResult;
use OBS\Result\UploadPartResult;
use OBS\Model\BucketListInfo;
use OBS\Model\LifecycleConfig;
use OBS\Model\RefererConfig;
use OBS\Model\WebsiteConfig;
use OBS\Core\ObsUtil;
use OBS\Model\ListPartsInfo;

/**
 * Class OssClient
 *
 * Object Storage Service(OBS)'s client class, which wraps all OBS APIs user could call to talk to OBS.
 * Users could do operations on bucket, object, including MultipartUpload or setting ACL via an OBSClient instance.
 * For more details, please check out the OBS API document:https://www.alibabacloud.com/help/doc-detail/31947.htm
 */
class ObsClient
{
    /**
     * Constructor
     *
     * There're a few different ways to create an OssClient object:
     * 1. Most common one from access Id, access Key and the endpoint: $obsClient = new OssClient($id, $key, $endpoint)
     * 2. If the endpoint is the CName (such as www.testobs.com, make sure it's CName binded in the OBS console),
     *    uses $obsClient = new OssClient($id, $key, $endpoint, true)
     * 3. If using Alicloud's security token service (STS), then the AccessKeyId, AccessKeySecret and STS token are all got from STS.
     * Use this: $obsClient = new OssClient($id, $key, $endpoint, false, $token)
     * 4. If the endpoint is in IP format, you could use this: $obsClient = new OssClient($id, $key, “1.2.3.4:8900”)
     *
     * @param string $accessKeyId The AccessKeyId from OBS or STS
     * @param string $accessKeySecret The AccessKeySecret from OBS or STS
     * @param string $endpoint The domain name of the datacenter,For example: obs-cn-hangzhou.huaweics.com
     * @param boolean $isCName If this is the CName and binded in the bucket.
     * @param string $securityToken from STS.
     * @param string $requestProxy
     * @throws ObsException
     */
    public function __construct($accessKeyId, $accessKeySecret, $endpoint, $isCName = false, $securityToken = NULL, $requestProxy = NULL)
    {
        $accessKeyId = trim($accessKeyId);
        $accessKeySecret = trim($accessKeySecret);
        $endpoint = trim(trim($endpoint), "/");

        if (empty($accessKeyId)) {
            throw new ObsException("access key id is empty");
        }
        if (empty($accessKeySecret)) {
            throw new ObsException("access key secret is empty");
        }
        if (empty($endpoint)) {
            throw new ObsException("endpoint is empty");
        }
        $this->hostname = $this->checkEndpoint($endpoint, $isCName);
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->securityToken = $securityToken;
        $this->requestProxy = $requestProxy;
        self::checkEnv();
    }

    /**
     * Lists the Bucket [GetService]. Not applicable if the endpoint is CName (because CName must be binded to a specific bucket).
     *
     * @param array $options
     * @throws ObsException
     * @return BucketListInfo
     */
    public function listBuckets($options = NULL)
    {
        if ($this->hostType === self::OBS_HOST_TYPE_CNAME) {
            throw new ObsException("operation is not permitted with CName host");
        }
        $this->precheckOptions($options);
        $options[self::OBS_BUCKET] = '';
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $response = $this->auth($options);

        $result = new ListBucketsResult($response);

        return $result->getData();
    }

    /**
     * Creates bucket,The ACL of the bucket created by default is OssClient::OBS_ACL_TYPE_PRIVATE
     *
     * @param string $bucket
     * @param string $acl
     * @param array $options
     * @param string $storageType
     * @return null
     */
    public function createBucket($bucket, $acl = self::OBS_ACL_TYPE_PRIVATE, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_HEADERS] = array(self::OBS_ACL => $acl);
        if (isset($options[self::OBS_STORAGE])) {
            $this->precheckStorage($options[self::OBS_STORAGE]);
            $options[self::OBS_CONTENT] = ObsUtil::createBucketXmlBody($options[self::OBS_STORAGE]);
            unset($options[self::OBS_STORAGE]);
        }
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Deletes bucket
     * The deletion will not succeed if the bucket is not empty (either has objects or parts)
     * To delete a bucket, all its objects and parts must be deleted first.
     *
     * @param string $bucket
     * @param array $options
     * @return null
     */
    public function deleteBucket($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = '/';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Checks if a bucket exists
     *
     * @param string $bucket
     * @return bool
     * @throws ObsException
     */
    public function doesBucketExist($bucket)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'acl';
        $response = $this->auth($options);
        $result = new ExistResult($response);
        return $result->getData();
    }

    /**
     * Get the data center location information for the bucket
     *
     * @param string $bucket
     * @param array $options
     * @throws ObsException
     * @return string
     */
    public function getBucketLocation($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'location';
        $response = $this->auth($options);
        $result = new GetLocationResult($response);
        return $result->getData();
    }

    /**
     * Get the Meta information for the Bucket
     *
     * @param string $bucket
     * @param array $options  Refer to the SDK documentation
     * @return array
     */
    public function getBucketMeta($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_HEAD;
        $options[self::OBS_OBJECT] = '/';
        $response = $this->auth($options);
        $result = new HeaderResult($response);
        return $result->getData();
    }

    /**
     * Gets the bucket ACL
     *
     * @param string $bucket
     * @param array $options
     * @throws ObsException
     * @return string
     */
    public function getBucketAcl($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'acl';
        $response = $this->auth($options);
        $result = new AclResult($response);
        return $result->getData();
    }

    /**
     * Sets the bucket ACL
     *
     * @param string $bucket bucket name
     * @param string $acl access permissions, valid values are ['private', 'public-read', 'public-read-write']
     * @param array $options by default is empty
     * @throws ObsException
     * @return null
     */
    public function putBucketAcl($bucket, $acl, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_HEADERS] = array(self::OBS_ACL => $acl);
        $options[self::OBS_SUB_RESOURCE] = 'acl';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets object ACL
     *
     * @param string $bucket
     * @param string $object
     * @throws ObsException
     * @return string
     */
    public function getObjectAcl($bucket, $object)
    {
        $options = array();
        $this->precheckCommon($bucket, $object, $options, true);
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_SUB_RESOURCE] = 'acl';
        $response = $this->auth($options);
        $result = new AclResult($response);
        return $result->getData();
    }

    /**
     * Sets the object ACL
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $acl access permissions, valid values are ['default', 'private', 'public-read', 'public-read-write']
     * @throws ObsException
     * @return null
     */
    public function putObjectAcl($bucket, $object, $acl)
    {
        $this->precheckCommon($bucket, $object, $options, true);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_HEADERS] = array(self::OBS_OBJECT_ACL => $acl);
        $options[self::OBS_SUB_RESOURCE] = 'acl';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the bucket logging config
     *
     * @param string $bucket bucket name
     * @param array $options by default is empty
     * @throws ObsException
     * @return LoggingConfig
     */
    public function getBucketLogging($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'logging';
        $response = $this->auth($options);
        $result = new GetLoggingResult($response);
        return $result->getData();
    }

    /**
     * Sets the bycket logging config. Only owner can call this API.
     *
     * @param string $bucket bucket name
     * @param string $targetBucket The logging file's bucket
     * @param string $targetPrefix The logging file's prefix
     * @param array $options By default is empty.
     * @throws ObsException
     * @return null
     */
    public function putBucketLogging($bucket, $targetBucket, $targetPrefix, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $this->precheckBucket($targetBucket, 'targetbucket is not allowed empty');
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'logging';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';

        $loggingConfig = new LoggingConfig($targetBucket, $targetPrefix);
        $options[self::OBS_CONTENT] = $loggingConfig->serializeToXml();
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Deletes the bucket logging config
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function deleteBucketLogging($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'logging';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Sets the website config in bucket---that is could make the bucket as a static website once the CName is binded.
     *
     * @param string $bucket bucket name
     * @param WebsiteConfig $websiteConfig
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function putBucketWebsite($bucket, $websiteConfig, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'website';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $options[self::OBS_CONTENT] = $websiteConfig->serializeToXml();
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the website config in the bucket
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return WebsiteConfig
     */
    public function getBucketWebsite($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'website';
        $response = $this->auth($options);
        $result = new GetWebsiteResult($response);
        return $result->getData();
    }

    /**
     * Deletes the website config in the bucket
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function deleteBucketWebsite($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'website';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Sets the crobs-origin-resource-sharing (CORS) rule. It would overwrite the originl one.
     *
     * @param string $bucket bucket name
     * @param CorsConfig $corsConfig CORS config. Check out the details from OBS API document
     * @param array $options array
     * @throws ObsException
     * @return null
     */
    public function putBucketCors($bucket, $corsConfig, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'cors';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $options[self::OBS_CONTENT] = $corsConfig->serializeToXml();
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the bucket CORS config
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return CorsConfig
     */
    public function getBucketCors($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'cors';
        $response = $this->auth($options);
        $result = new GetCorsResult($response, __FUNCTION__);
        return $result->getData();
    }

    /**
     * Deletes the bucket's CORS config and disable the CORS on the bucket.
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function deleteBucketCors($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'cors';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Bind a CName for the bucket
     *
     * @param string $bucket bucket name
     * @param string $cname
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function addBucketCname($bucket, $cname, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'cname';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $cnameConfig = new CnameConfig();
        $cnameConfig->addCname($cname);
        $options[self::OBS_CONTENT] = $cnameConfig->serializeToXml();
        $options[self::OBS_COMP] = 'add';

        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the binded CName list of the bucket
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return CnameConfig
     */
    public function getBucketCname($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'cname';
        $response = $this->auth($options);
        $result = new GetCnameResult($response);
        return $result->getData();
    }

    /**
     * Remove a CName binding from the bucket
     *
     * @param string $bucket bucket name
     * @param CnameConfig $cnameConfig
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function deleteBucketCname($bucket, $cname, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'cname';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $cnameConfig = new CnameConfig();
        $cnameConfig->addCname($cname);
        $options[self::OBS_CONTENT] = $cnameConfig->serializeToXml();
        $options[self::OBS_COMP] = 'delete';

        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Creates a Live Channel under a bucket
     *
     * @param string $bucket bucket name
     * @param string channelName  $channelName
     * @param LiveChannelConfig $channelConfig
     * @param array $options
     * @throws ObsException
     * @return LiveChannelInfo
     */
    public function putBucketLiveChannel($bucket, $channelName, $channelConfig, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $channelName;
        $options[self::OBS_SUB_RESOURCE] = 'live';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $options[self::OBS_CONTENT] = $channelConfig->serializeToXml();

        $response = $this->auth($options);
        $result = new PutLiveChannelResult($response);
        $info = $result->getData();
        $info->setName($channelName);
        $info->setDescription($channelConfig->getDescription());
        
        return $info;
    }

    /**
     * Sets the LiveChannel status
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName
     * @param string channelStatus $channelStatus enabled or disabled
     * @param array $options
     * @throws ObsException
     * @return null 
     */
    public function putLiveChannelStatus($bucket, $channelName, $channelStatus, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $channelName;
        $options[self::OBS_SUB_RESOURCE] = 'live';
        $options[self::OBS_LIVE_CHANNEL_STATUS] = $channelStatus;

        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the LiveChannel information by the channel name
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName
     * @param array $options
     * @throws ObsException
     * @return GetLiveChannelInfo
     */
    public function getLiveChannelInfo($bucket, $channelName, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = $channelName;
        $options[self::OBS_SUB_RESOURCE] = 'live';

        $response = $this->auth($options);
        $result = new GetLiveChannelInfoResult($response);
        return $result->getData();
    }

    /**
     * Gets the status of LiveChannel
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName
     * @param array $options
     * @throws ObsException
     * @return GetLiveChannelStatus
     */
    public function getLiveChannelStatus($bucket, $channelName, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = $channelName;
        $options[self::OBS_SUB_RESOURCE] = 'live';
        $options[self::OBS_COMP] = 'stat';
      
        $response = $this->auth($options);
        $result = new GetLiveChannelStatusResult($response);
        return $result->getData();
    }

     /**
     * Gets the LiveChannel pushing streaming record
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName
     * @param array $options
     * @throws ObsException
     * @return GetLiveChannelHistory
     */
   public function getLiveChannelHistory($bucket, $channelName, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = $channelName;
        $options[self::OBS_SUB_RESOURCE] = 'live';
        $options[self::OBS_COMP] = 'history';

        $response = $this->auth($options);
        $result = new GetLiveChannelHistoryResult($response);
        return $result->getData();
    }
  
    /**
     *Gets the live channel list under a bucket.
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return LiveChannelListInfo
     */
    public function listBucketLiveChannels($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'live';
        $options[self::OBS_QUERY_STRING] = array(
            'prefix' => isset($options['prefix']) ? $options['prefix'] : '',
            'marker' => isset($options['marker']) ? $options['marker'] : '',
            'max-keys' => isset($options['max-keys']) ? $options['max-keys'] : '',
        );
        $response = $this->auth($options);
        $result = new ListLiveChannelResult($response);
        $list = $result->getData();
        $list->setBucketName($bucket);

        return $list;
    }

    /**
     * Creates a play list file for the LiveChannel
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName 
     * @param string $playlistName The playlist name, must end with ".m3u8".
     * @param array $setTime  startTime and EndTime in unix time. No more than 1 day.
     * @throws ObsException
     * @return null
     */
    public function postVodPlaylist($bucket, $channelName, $playlistName, $setTime)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_OBJECT] = $channelName . '/' . $playlistName;
        $options[self::OBS_SUB_RESOURCE] = 'vod';
        $options[self::OBS_LIVE_CHANNEL_END_TIME] = $setTime['EndTime'];
        $options[self::OBS_LIVE_CHANNEL_START_TIME] = $setTime['StartTime'];
       
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Deletes the Bucket LiveChannel
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function deleteBucketLiveChannel($bucket, $channelName, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = $channelName;
        $options[self::OBS_SUB_RESOURCE] = 'live';

        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Generates the signed pushing streaming url
     *
     * @param string $bucket bucket name
     * @param string channelName $channelName
     * @param int timeout timeout value in seconds
     * @param array $options
     * @throws ObsException
     * @return The signed pushing streaming url
     */
    public function signRtmpUrl($bucket, $channelName, $timeout = 60, $options = NULL)
    {
        $this->precheckCommon($bucket, $channelName, $options, false);
        $expires = time() + $timeout;
        $proto = 'rtmp://';
        $hostname = $this->generateHostname($bucket);
        $cano_params = '';
        $query_items = array();
        $params = isset($options['params']) ? $options['params'] : array();
        uksort($params, 'strnatcasecmp');
        foreach ($params as $key => $value) {
            $cano_params = $cano_params . $key . ':' . $value . "\n";
            $query_items[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        $resource = '/' . $bucket . '/' . $channelName;

        $string_to_sign = $expires . "\n" . $cano_params . $resource;
        $signature = ObsUtil::base64UrlSafeEncode(hash_hmac('sha1', $string_to_sign, $this->accessKeySecret, true));

        $query_items[] = 'OBSAccessKeyId=' . rawurlencode($this->accessKeyId);
        $query_items[] = 'Expires=' . rawurlencode($expires);
        $query_items[] = 'Signature=' . rawurlencode($signature);

        return $proto . $hostname . '/live/' . $channelName . '?' . implode('&', $query_items);
    }

    /**
     * Precheck the CORS request. Before sending a CORS request, a preflight request (OPTIONS) is sent with the specific origin.
     * HTTP METHOD and headers information are sent to OBS as well for evaluating if the CORS request is allowed. 
     * 
     * Note: OBS could enable the CORS on the bucket by calling putBucketCors. Once CORS is enabled, the OBS could evaluate accordingto the preflight request.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $origin the origin of the request
     * @param string $request_method The actual HTTP method which will be used in CORS request
     * @param string $request_headers The actual HTTP headers which will be used in CORS request
     * @param array $options
     * @return array
     * @throws ObsException
     * @link http://help.huawei.com/document_detail/obs/api-reference/cors/OptionObject.html
     */
    public function optionsObject($bucket, $object, $origin, $request_method, $request_headers, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_OPTIONS;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_HEADERS] = array(
            self::OBS_OPTIONS_ORIGIN => $origin,
            self::OBS_OPTIONS_REQUEST_HEADERS => $request_headers,
            self::OBS_OPTIONS_REQUEST_METHOD => $request_method
        );
        $response = $this->auth($options);
        $result = new HeaderResult($response);
        return $result->getData();
    }

    /**
     * Sets the bucket's lifecycle config
     *
     * @param string $bucket bucket name
     * @param LifecycleConfig $lifecycleConfig LifecycleConfig instance
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function putBucketLifecycle($bucket, $lifecycleConfig, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'lifecycle';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $options[self::OBS_CONTENT] = $lifecycleConfig->serializeToXml();
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets bucket's lifecycle config
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return LifecycleConfig
     */
    public function getBucketLifecycle($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'lifecycle';
        $response = $this->auth($options);
        $result = new GetLifecycleResult($response);
        return $result->getData();
    }

    /**
     * Deletes the bucket's lifecycle config
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return null
     */
    public function deleteBucketLifecycle($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'lifecycle';
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Sets a bucket's referer, which has a whitelist of referrer and specifies if empty referer is allowed.
     * Checks out API document for more details about "Bucket Referer" 
     *
     * @param string $bucket bucket name
     * @param RefererConfig $refererConfig
     * @param array $options
     * @return ResponseCore
     * @throws null
     */
    public function putBucketReferer($bucket, $refererConfig, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'referer';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $options[self::OBS_CONTENT] = $refererConfig->serializeToXml();
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the bucket's Referer
     * Checks out API document for more details about "Bucket Referer" 
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return RefererConfig
     */
    public function getBucketReferer($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'referer';
        $response = $this->auth($options);
        $result = new GetRefererResult($response);
        return $result->getData();
    }


    /**
     * Set the size of the bucket,the unit is GB
     * When the capacity of the bucket is bigger than the set, it's forbidden to continue writing
     *
     * @param string $bucket bucket name
     * @param int $storageCapacity
     * @param array $options
     * @return ResponseCore
     * @throws null
     */
    public function putBucketStorageCapacity($bucket, $storageCapacity, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'qos';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $storageCapacityConfig = new StorageCapacityConfig($storageCapacity);
        $options[self::OBS_CONTENT] = $storageCapacityConfig->serializeToXml();
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Get the capacity of the bucket, the unit is GB
     *
     * @param string $bucket bucket name
     * @param array $options
     * @throws ObsException
     * @return int
     */
    public function getBucketStorageCapacity($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'qos';
        $response = $this->auth($options);
        $result = new GetStorageCapacityResult($response);
        return $result->getData();
    }


    /**
     * Lists the bucket's object list (in ObjectListInfo)
     *
     * @param string $bucket
     * @param array $options are defined below:
     * $options = array(
     *      'max-keys'  => specifies max object count to return. By default is 100 and max value could be 1000.
     *      'prefix'    => specifies the key prefix the returned objects must have. Note that the returned keys still contain the prefix.
     *      'delimiter' => The delimiter of object name for grouping object. When it's specified, listObjects will differeniate the object and folder. And it will return subfolder's objects.
     *      'marker'    => The key of returned object must be greater than the 'marker'.
     *)
     * Prefix and marker are for filtering and paging. Their length must be less than 256 bytes
     * @throws ObsException
     * @return ObjectListInfo
     */
    public function listObjects($bucket, $options = NULL)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_HEADERS] = array(
            self::OBS_DELIMITER => isset($options[self::OBS_DELIMITER]) ? $options[self::OBS_DELIMITER] : '/',
            self::OBS_PREFIX => isset($options[self::OBS_PREFIX]) ? $options[self::OBS_PREFIX] : '',
            self::OBS_MAX_KEYS => isset($options[self::OBS_MAX_KEYS]) ? $options[self::OBS_MAX_KEYS] : self::OBS_MAX_KEYS_VALUE,
            self::OBS_MARKER => isset($options[self::OBS_MARKER]) ? $options[self::OBS_MARKER] : '',
        );
        $query = isset($options[self::OBS_QUERY_STRING]) ? $options[self::OBS_QUERY_STRING] : array();
        $options[self::OBS_QUERY_STRING] = array_merge(
            $query,
            array(self::OBS_ENCODING_TYPE => self::OBS_ENCODING_TYPE_URL)
        );

        $response = $this->auth($options);
        $result = new ListObjectsResult($response);
        return $result->getData();
    }

    /**
     * Creates a virtual 'folder' in OBS. The name should not end with '/' because the method will append the name with a '/' anyway.
     *
     * Internal use only.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param array $options
     * @return null
     */
    public function createObjectDir($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $object . '/';
        $options[self::OBS_CONTENT_LENGTH] = array(self::OBS_CONTENT_LENGTH => 0);
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Uploads the $content object to OBS.
     *
     * @param string $bucket bucket name
     * @param string $object objcet name
     * @param string $content The content object
     * @param array $options
     * @return null
     */
    public function putObject($bucket, $object, $content, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);

        $options[self::OBS_CONTENT] = $content;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $object;

        if (!isset($options[self::OBS_LENGTH])) {
            $options[self::OBS_CONTENT_LENGTH] = strlen($options[self::OBS_CONTENT]);
        } else {
            $options[self::OBS_CONTENT_LENGTH] = $options[self::OBS_LENGTH];
        }

        $is_check_md5 = $this->isCheckMD5($options);
        if ($is_check_md5) {
        	$content_md5 = ObsUtil::base64UrlSafeEncode(md5($content, true));
        	$options[self::OBS_CONTENT_MD5] = $content_md5;
        }
        
        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = $this->getMimeType($object);
        }
        $response = $this->auth($options);
        
        if (isset($options[self::OBS_CALLBACK]) && !empty($options[self::OBS_CALLBACK])) {
            $result = new CallbackResult($response);
        } else {
            $result = new PutSetDeleteResult($response);
        }
            
        return $result->getData();
    }


    /**
     * creates symlink
     * @param string $bucket bucket name
     * @param string $symlink symlink name
     * @param string $targetObject targetObject name
     * @param array $options
     * @return null
     */
    public function putSymlink($bucket, $symlink ,$targetObject, $options = NULL)
    {
        $this->precheckCommon($bucket, $symlink, $options);

        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $symlink;
        $options[self::OBS_SUB_RESOURCE] = self::OBS_SYMLINK;
        $options[self::OBS_HEADERS][self::OBS_SYMLINK_TARGET] = rawurlencode($targetObject);

        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * gets symlink
     *@param string $bucket bucket name
     * @param string $symlink symlink name
     * @return null
     */
    public function getSymlink($bucket, $symlink)
    {
        $this->precheckCommon($bucket, $symlink, $options);

        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = $symlink;
        $options[self::OBS_SUB_RESOURCE] = self::OBS_SYMLINK;

        $response = $this->auth($options);
        $result = new SymlinkResult($response);
        return $result->getData();
    }

    /**
     * Uploads a local file
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $file local file path
     * @param array $options
     * @return null
     * @throws ObsException
     */
    public function uploadFile($bucket, $object, $file, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        ObsUtil::throwOssExceptionWithMessageIfEmpty($file, "file path is invalid");
        $file = ObsUtil::encodePath($file);
        if (!file_exists($file)) {
            throw new ObsException($file . " file does not exist");
        }
        $options[self::OBS_FILE_UPLOAD] = $file;
        $file_size = filesize($options[self::OBS_FILE_UPLOAD]);
        $is_check_md5 = $this->isCheckMD5($options);
        if ($is_check_md5) {
            $content_md5 = ObsUtil::base64UrlSafeEncode(md5_file($options[self::OBS_FILE_UPLOAD], true));
            $options[self::OBS_CONTENT_MD5] = $content_md5;
        }
        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = $this->getMimeType($object, $file);
        }
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_CONTENT_LENGTH] = $file_size;
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Append the object with the content at the specified position.
     * The specified position is typically the lengh of the current file.
     * @param string $bucket bucket name
     * @param string $object objcet name
     * @param string $content content to append
     * @param array $options
     * @return int next append position
     * @throws ObsException
     */
    public function appendObject($bucket, $object, $content, $position, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);

        $options[self::OBS_CONTENT] = $content;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_SUB_RESOURCE] = 'append';
        $options[self::OBS_POSITION] = strval($position);

        if (!isset($options[self::OBS_LENGTH])) {
            $options[self::OBS_CONTENT_LENGTH] = strlen($options[self::OBS_CONTENT]);
        } else {
            $options[self::OBS_CONTENT_LENGTH] = $options[self::OBS_LENGTH];
        }
        
        $is_check_md5 = $this->isCheckMD5($options);
        if ($is_check_md5) {
        	$content_md5 = ObsUtil::base64UrlSafeEncode(md5($content, true));
        	$options[self::OBS_CONTENT_MD5] = $content_md5;
        }

        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = $this->getMimeType($object);
        }
        $response = $this->auth($options);
        $result = new AppendResult($response);
        return $result->getData();
    }

    /**
     * Append the object with a local file
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $file The local file path to append with
     * @param array $options
     * @return int next append position
     * @throws ObsException
     */
    public function appendFile($bucket, $object, $file, $position, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);

        ObsUtil::throwOssExceptionWithMessageIfEmpty($file, "file path is invalid");
        $file = ObsUtil::encodePath($file);
        if (!file_exists($file)) {
            throw new ObsException($file . " file does not exist");
        }
        $options[self::OBS_FILE_UPLOAD] = $file;
        $file_size = filesize($options[self::OBS_FILE_UPLOAD]);
        $is_check_md5 = $this->isCheckMD5($options);
        if ($is_check_md5) {
            $content_md5 = ObsUtil::base64UrlSafeEncode(md5_file($options[self::OBS_FILE_UPLOAD], true));
            $options[self::OBS_CONTENT_MD5] = $content_md5;
        }
        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = $this->getMimeType($object, $file);
        }

        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_CONTENT_LENGTH] = $file_size;
        $options[self::OBS_SUB_RESOURCE] = 'append';
        $options[self::OBS_POSITION] = strval($position);

        $response = $this->auth($options);
        $result = new AppendResult($response);
        return $result->getData();
    }

    /**
     * Copy from an existing OBS object to another OBS object. If the target object exists already, it will be overwritten.
     *
     * @param string $fromBucket Source bucket name
     * @param string $fromObject Source object name
     * @param string $toBucket Target bucket name
     * @param string $toObject Target object name
     * @param array $options
     * @return null
     * @throws ObsException
     */
    public function copyObject($fromBucket, $fromObject, $toBucket, $toObject, $options = NULL)
    {
        $this->precheckCommon($fromBucket, $fromObject, $options);
        $this->precheckCommon($toBucket, $toObject, $options);
        $options[self::OBS_BUCKET] = $toBucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_OBJECT] = $toObject;
        if (isset($options[self::OBS_HEADERS])) {
            $options[self::OBS_HEADERS][self::OBS_OBJECT_COPY_SOURCE] = '/' . $fromBucket . '/' . $fromObject;
        } else {
            $options[self::OBS_HEADERS] = array(self::OBS_OBJECT_COPY_SOURCE => '/' . $fromBucket . '/' . $fromObject);
        }
        $response = $this->auth($options);
        $result = new CopyObjectResult($response);
        return $result->getData();
    }

    /**
     * Gets Object metadata
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $options Checks out the SDK document for the detail
     * @return array
     */
    public function getObjectMeta($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_HEAD;
        $options[self::OBS_OBJECT] = $object;
        $response = $this->auth($options);
        $result = new HeaderResult($response);
        return $result->getData();
    }

    /**
     * Deletes a object
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param array $options
     * @return null
     */
    public function deleteObject($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_OBJECT] = $object;
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Deletes multiple objects in a bucket
     *
     * @param string $bucket bucket name
     * @param array $objects object list
     * @param array $options
     * @return ResponseCore
     * @throws null
     */
    public function deleteObjects($bucket, $objects, $options = null)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        if (!is_array($objects) || !$objects) {
            throw new ObsException('objects must be array');
        }
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'delete';
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        $quiet = 'false';
        if (isset($options['quiet'])) {
            if (is_bool($options['quiet'])) { //Boolean
                $quiet = $options['quiet'] ? 'true' : 'false';
            } elseif (is_string($options['quiet'])) { // string
                $quiet = ($options['quiet'] === 'true') ? 'true' : 'false';
            }
        }
        $xmlBody = ObsUtil::createDeleteObjectsXmlBody($objects, $quiet);
        $options[self::OBS_CONTENT] = $xmlBody;
        $response = $this->auth($options);
        $result = new DeleteObjectsResult($response);
        return $result->getData();
    }

    /**
     * Gets Object content
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param array $options It must contain ALIOBS::OBS_FILE_DOWNLOAD. And ALIOBS::OBS_RANGE is optional and empty means to download the whole file.
     * @return string
     */
    public function getObject($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_OBJECT] = $object;
        if (isset($options[self::OBS_LAST_MODIFIED])) {
            $options[self::OBS_HEADERS][self::OBS_IF_MODIFIED_SINCE] = $options[self::OBS_LAST_MODIFIED];
            unset($options[self::OBS_LAST_MODIFIED]);
        }
        if (isset($options[self::OBS_ETAG])) {
            $options[self::OBS_HEADERS][self::OBS_IF_NONE_MATCH] = $options[self::OBS_ETAG];
            unset($options[self::OBS_ETAG]);
        }
        if (isset($options[self::OBS_RANGE])) {
            $range = $options[self::OBS_RANGE];
            $options[self::OBS_HEADERS][self::OBS_RANGE] = "bytes=$range";
            unset($options[self::OBS_RANGE]);
        }
        $response = $this->auth($options);
        $result = new BodyResult($response);
        return $result->getData();
    }

    /**
     * Checks if the object exists
     * It's implemented by getObjectMeta().
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param array $options
     * @return bool True:object exists; False:object does not exist
     */
    public function doesObjectExist($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_HEAD;
        $options[self::OBS_OBJECT] = $object;
        $response = $this->auth($options);
        $result = new ExistResult($response);
        return $result->getData();
    }

    /**
     * Object reading for Archive type
     * Use Restore to enable the server to perform the thawing task
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @return null
     * @throws ObsException
     */
    public function restoreObject($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_SUB_RESOURCE] = self::OBS_RESTORE;
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Gets the part size according to the preferred part size.
     * If the specified part size is too small or too big, it will return a min part or max part size instead.
     * Otherwise returns the specified part size.
     * @param int $partSize
     * @return int
     */
    private function computePartSize($partSize)
    {
        $partSize = (integer)$partSize;
        if ($partSize <= self::OBS_MIN_PART_SIZE) {
            $partSize = self::OBS_MIN_PART_SIZE;
        } elseif ($partSize > self::OBS_MAX_PART_SIZE) {
            $partSize = self::OBS_MAX_PART_SIZE;
        }
        return $partSize;
    }

    /**
     * Computes the parts count, size and start position according to the file size and the part size.
     * It must be only called by upload_Part().
     *
     * @param integer $file_size File size
     * @param integer $partSize part大小,part size. Default is 5MB
     * @return array An array contains key-value pairs--the key is `seekTo`and value is `length`.
     */
    public function generateMultiuploadParts($file_size, $partSize = 5242880)
    {
        $i = 0;
        $size_count = $file_size;
        $values = array();
        $partSize = $this->computePartSize($partSize);
        while ($size_count > 0) {
            $size_count -= $partSize;
            $values[] = array(
                self::OBS_SEEK_TO => ($partSize * $i),
                self::OBS_LENGTH => (($size_count > 0) ? $partSize : ($size_count + $partSize)),
            );
            $i++;
        }
        return $values;
    }

    /**
     * Initialize a multi-part upload
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param array $options Key-Value array
     * @throws ObsException
     * @return string returns uploadid
     */
    public function initiateMultipartUpload($bucket, $object, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_SUB_RESOURCE] = 'uploads';
        $options[self::OBS_CONTENT] = '';

        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = $this->getMimeType($object);
        }
        if (!isset($options[self::OBS_HEADERS])) {
            $options[self::OBS_HEADERS] = array();
        }
        $response = $this->auth($options);
        $result = new InitiateMultipartUploadResult($response);
        return $result->getData();
    }

    /**
     * Upload a part in a multiparts upload.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $uploadId
     * @param array $options Key-Value array
     * @return string eTag
     * @throws ObsException
     */
    public function uploadPart($bucket, $object, $uploadId, $options = null)
    {
        $this->precheckCommon($bucket, $object, $options);
        $this->precheckParam($options, self::OBS_FILE_UPLOAD, __FUNCTION__);
        $this->precheckParam($options, self::OBS_PART_NUM, __FUNCTION__);

        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_UPLOAD_ID] = $uploadId;

        if (isset($options[self::OBS_LENGTH])) {
            $options[self::OBS_CONTENT_LENGTH] = $options[self::OBS_LENGTH];
        }
        $response = $this->auth($options);
        $result = new UploadPartResult($response);
        return $result->getData();
    }

    /**
     * Gets the uploaded parts.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $uploadId uploadId
     * @param array $options Key-Value array
     * @return ListPartsInfo
     * @throws ObsException
     */
    public function listParts($bucket, $object, $uploadId, $options = null)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_UPLOAD_ID] = $uploadId;
        $options[self::OBS_QUERY_STRING] = array();
        foreach (array('max-parts', 'part-number-marker') as $param) {
            if (isset($options[$param])) {
                $options[self::OBS_QUERY_STRING][$param] = $options[$param];
                unset($options[$param]);
            }
        }
        $response = $this->auth($options);
        $result = new ListPartsResult($response);
        return $result->getData();
    }

    /**
     * Abort a multiparts upload
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $uploadId uploadId
     * @param array $options Key-Value name
     * @return null
     * @throws ObsException
     */
    public function abortMultipartUpload($bucket, $object, $uploadId, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_METHOD] = self::OBS_HTTP_DELETE;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_UPLOAD_ID] = $uploadId;
        $response = $this->auth($options);
        $result = new PutSetDeleteResult($response);
        return $result->getData();
    }

    /**
     * Completes a multiparts upload, after all parts are uploaded.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $uploadId uploadId
     * @param array $listParts array( array("PartNumber"=> int, "ETag"=>string))
     * @param array $options Key-Value array
     * @throws ObsException
     * @return null
     */
    public function completeMultipartUpload($bucket, $object, $uploadId, $listParts, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        $options[self::OBS_METHOD] = self::OBS_HTTP_POST;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_UPLOAD_ID] = $uploadId;
        $options[self::OBS_CONTENT_TYPE] = 'application/xml';
        if (!is_array($listParts)) {
            throw new ObsException("listParts must be array type");
        }
        $options[self::OBS_CONTENT] = ObsUtil::createCompleteMultipartUploadXmlBody($listParts);
        $response = $this->auth($options);
        if (isset($options[self::OBS_CALLBACK]) && !empty($options[self::OBS_CALLBACK])) {
            $result = new CallbackResult($response);
        } else {
            $result = new PutSetDeleteResult($response);
        }
        return $result->getData();
    }

    /**
     * Lists all ongoing multipart upload events, which means all initialized but not completed or aborted multipart uploads.
     *
     * @param string $bucket bucket
     * @param array $options key-value array--expected keys are 'delimiter', 'key-marker', 'max-uploads', 'prefix', 'upload-id-marker'
     * @throws ObsException
     * @return ListMultipartUploadInfo
     */
    public function listMultipartUploads($bucket, $options = null)
    {
        $this->precheckCommon($bucket, NULL, $options, false);
        $options[self::OBS_METHOD] = self::OBS_HTTP_GET;
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = '/';
        $options[self::OBS_SUB_RESOURCE] = 'uploads';

        foreach (array('delimiter', 'key-marker', 'max-uploads', 'prefix', 'upload-id-marker') as $param) {
            if (isset($options[$param])) {
                $options[self::OBS_QUERY_STRING][$param] = $options[$param];
                unset($options[$param]);
            }
        }
        $query = isset($options[self::OBS_QUERY_STRING]) ? $options[self::OBS_QUERY_STRING] : array();
        $options[self::OBS_QUERY_STRING] = array_merge(
            $query,
            array(self::OBS_ENCODING_TYPE => self::OBS_ENCODING_TYPE_URL)
        );

        $response = $this->auth($options);
        $result = new ListMultipartUploadResult($response);
        return $result->getData();
    }

    /**
     * Copy an existing file as a part
     *
     * @param string $fromBucket source bucket name
     * @param string $fromObject source object name
     * @param string $toBucket target bucket name
     * @param string $toObject target object name
     * @param int $partNumber Part number
     * @param string $uploadId Upload Id
     * @param array $options Key-Value array---it should have 'start' or 'end' key to specify the range of the source object to copy. If it's not specifed, the whole object is copied.
     * @return null
     * @throws ObsException
     */
    public function uploadPartCopy($fromBucket, $fromObject, $toBucket, $toObject, $partNumber, $uploadId, $options = NULL)
    {
        $this->precheckCommon($fromBucket, $fromObject, $options);
        $this->precheckCommon($toBucket, $toObject, $options);

        //If $options['isFullCopy'] is not set, copy from the beginning
        $start_range = "0";
        if (isset($options['start'])) {
            $start_range = $options['start'];
        }
        $end_range = "";
        if (isset($options['end'])) {
            $end_range = $options['end'];
        }
        $options[self::OBS_METHOD] = self::OBS_HTTP_PUT;
        $options[self::OBS_BUCKET] = $toBucket;
        $options[self::OBS_OBJECT] = $toObject;
        $options[self::OBS_PART_NUM] = $partNumber;
        $options[self::OBS_UPLOAD_ID] = $uploadId;

        if (!isset($options[self::OBS_HEADERS])) {
            $options[self::OBS_HEADERS] = array();
        }

        $options[self::OBS_HEADERS][self::OBS_OBJECT_COPY_SOURCE] = '/' . $fromBucket . '/' . $fromObject;
        $options[self::OBS_HEADERS][self::OBS_OBJECT_COPY_SOURCE_RANGE] = "bytes=" . $start_range . "-" . $end_range;
        $response = $this->auth($options);
        $result = new UploadPartResult($response);
        return $result->getData();
    }

    /**
     * A higher level API for uploading a file with multipart upload. It consists of initialization, parts upload and completion.
     *
     * @param string $bucket bucket name
     * @param string $object object name
     * @param string $file The local file to upload
     * @param array $options Key-Value array
     * @return null
     * @throws ObsException
     */
    public function multiuploadFile($bucket, $object, $file, $options = null)
    {
        $this->precheckCommon($bucket, $object, $options);
        if (isset($options[self::OBS_LENGTH])) {
            $options[self::OBS_CONTENT_LENGTH] = $options[self::OBS_LENGTH];
            unset($options[self::OBS_LENGTH]);
        }
        if (empty($file)) {
            throw new ObsException("parameter invalid, file is empty");
        }
        $uploadFile = ObsUtil::encodePath($file);
        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = $this->getMimeType($object, $uploadFile);
        }

        $upload_position = isset($options[self::OBS_SEEK_TO]) ? (integer)$options[self::OBS_SEEK_TO] : 0;

        if (isset($options[self::OBS_CONTENT_LENGTH])) {
            $upload_file_size = (integer)$options[self::OBS_CONTENT_LENGTH];
        } else {
            $upload_file_size = filesize($uploadFile);
            if ($upload_file_size !== false) {
                $upload_file_size -= $upload_position;
            }
        }

        if ($upload_position === false || !isset($upload_file_size) || $upload_file_size === false || $upload_file_size < 0) {
            throw new ObsException('The size of `fileUpload` cannot be determined in ' . __FUNCTION__ . '().');
        }
        // Computes the part size and assign it to options.
        if (isset($options[self::OBS_PART_SIZE])) {
            $options[self::OBS_PART_SIZE] = $this->computePartSize($options[self::OBS_PART_SIZE]);
        } else {
            $options[self::OBS_PART_SIZE] = self::OBS_MID_PART_SIZE;
        }

        $is_check_md5 = $this->isCheckMD5($options);
        // if the file size is less than part size, use simple file upload.
        if ($upload_file_size < $options[self::OBS_PART_SIZE] && !isset($options[self::OBS_UPLOAD_ID])) {
            return $this->uploadFile($bucket, $object, $uploadFile, $options);
        }

        // Using multipart upload, initialize if no OBS_UPLOAD_ID is specified in options.
        if (isset($options[self::OBS_UPLOAD_ID])) {
            $uploadId = $options[self::OBS_UPLOAD_ID];
        } else {
            // initialize
            $uploadId = $this->initiateMultipartUpload($bucket, $object, $options);
        }

        // generates the parts information and upload them one by one
        $pieces = $this->generateMultiuploadParts($upload_file_size, (integer)$options[self::OBS_PART_SIZE]);
        $response_upload_part = array();
        foreach ($pieces as $i => $piece) {
            $from_pos = $upload_position + (integer)$piece[self::OBS_SEEK_TO];
            $to_pos = (integer)$piece[self::OBS_LENGTH] + $from_pos - 1;
            $up_options = array(
                self::OBS_FILE_UPLOAD => $uploadFile,
                self::OBS_PART_NUM => ($i + 1),
                self::OBS_SEEK_TO => $from_pos,
                self::OBS_LENGTH => $to_pos - $from_pos + 1,
                self::OBS_CHECK_MD5 => $is_check_md5,
            );
            if ($is_check_md5) {
                $content_md5 = ObsUtil::getMd5SumForFile($uploadFile, $from_pos, $to_pos);
                $up_options[self::OBS_CONTENT_MD5] = $content_md5;
            }
            $response_upload_part[] = $this->uploadPart($bucket, $object, $uploadId, $up_options);
        }

        $uploadParts = array();
        foreach ($response_upload_part as $i => $etag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $etag,
            );
        }
        return $this->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    }

    /**
     * Uploads the local directory to the specified bucket into specified folder (prefix)
     *
     * @param string $bucket bucket name
     * @param string $prefix The object key prefix. Typically it's folder name. The name should not end with '/' as the API appends it automatically.
     * @param string $localDirectory The local directory to upload
     * @param string $exclude To excluded directories
     * @param bool $recursive Recursive flag. True: Recursively upload all datas under the local directory; False: only upload first layer's files.
     * @param bool $checkMd5
     * @return array Returns two list: array("succeededList" => array("object"), "failedList" => array("object"=>"errorMessage"))
     * @throws ObsException
     */
    public function uploadDir($bucket, $prefix, $localDirectory, $exclude = '.|..|.svn|.git', $recursive = false, $checkMd5 = true)
    {
        $retArray = array("succeededList" => array(), "failedList" => array());
        if (empty($bucket)) throw new ObsException("parameter error, bucket is empty");
        if (!is_string($prefix)) throw new ObsException("parameter error, prefix is not string");
        if (empty($localDirectory)) throw new ObsException("parameter error, localDirectory is empty");
        $directory = $localDirectory;
        $directory = ObsUtil::encodePath($directory);
        //If it's not the local directory, throw OBSException.
        if (!is_dir($directory)) {
            throw new ObsException('parameter error: ' . $directory . ' is not a directory, please check it');
        }
        //read directory
        $file_list_array = ObsUtil::readDir($directory, $exclude, $recursive);
        if (!$file_list_array) {
            throw new ObsException($directory . ' is empty...');
        }
        foreach ($file_list_array as $k => $item) {
            if (is_dir($item['path'])) {
                continue;
            }
            $options = array(
                self::OBS_PART_SIZE => self::OBS_MIN_PART_SIZE,
                self::OBS_CHECK_MD5 => $checkMd5,
            );
            $realObject = (!empty($prefix) ? $prefix . '/' : '') . $item['file'];

            try {
                $this->multiuploadFile($bucket, $realObject, $item['path'], $options);
                $retArray["succeededList"][] = $realObject;
            } catch (ObsException $e) {
                $retArray["failedList"][$realObject] = $e->getMessage();
            }
        }
        return $retArray;
    }

    /**
     * Sign URL with specified expiration time in seconds (timeout) and HTTP method.
     * The signed URL could be used to access the object directly.
     *
     * @param string $bucket
     * @param string $object
     * @param int $timeout expiration time in seconds.
     * @param string $method
     * @param array $options Key-Value array
     * @return string
     * @throws ObsException
     */
    public function signUrl($bucket, $object, $timeout = 60, $method = self::OBS_HTTP_GET, $options = NULL)
    {
        $this->precheckCommon($bucket, $object, $options);
        //method
        if (self::OBS_HTTP_GET !== $method && self::OBS_HTTP_PUT !== $method) {
            throw new ObsException("method is invalid");
        }
        $options[self::OBS_BUCKET] = $bucket;
        $options[self::OBS_OBJECT] = $object;
        $options[self::OBS_METHOD] = $method;
        if (!isset($options[self::OBS_CONTENT_TYPE])) {
            $options[self::OBS_CONTENT_TYPE] = '';
        }
        $timeout = time() + $timeout;
        $options[self::OBS_PREAUTH] = $timeout;
        $options[self::OBS_DATE] = $timeout;
        $this->setSignStsInUrl(true);
        return $this->auth($options);
    }

    /**
     * validates options. Create a empty array if it's NULL.
     *
     * @param array $options
     * @throws ObsException
     */
    private function precheckOptions(&$options)
    {
        ObsUtil::validateOptions($options);
        if (!$options) {
            $options = array();
        }
    }

    /**
     * Validates bucket parameter
     *
     * @param string $bucket
     * @param string $errMsg
     * @throws ObsException
     */
    private function precheckBucket($bucket, $errMsg = 'bucket is not allowed empty')
    {
        ObsUtil::throwOssExceptionWithMessageIfEmpty($bucket, $errMsg);
    }

    /**
     * validates object parameter
     *
     * @param string $object
     * @throws ObsException
     */
    private function precheckObject($object)
    {
        ObsUtil::throwOssExceptionWithMessageIfEmpty($object, "object name is empty");
    }

    /**
     * 校验option restore
     *
     * @param string $restore
     * @throws ObsException
     */
    private function precheckStorage($storage)
    {
        if (is_string($storage)) {
            switch ($storage) {
                case self::OBS_STORAGE_ARCHIVE:
                    return;
                case self::OBS_STORAGE_IA:
                    return;
                case self::OBS_STORAGE_STANDARD:
                    return;
                default:
                    break;
            }
        }
        throw new ObsException('storage name is invalid');
    }

    /**
     * Validates bucket,options parameters and optionally validate object parameter.
     *
     * @param string $bucket
     * @param string $object
     * @param array $options
     * @param bool $isCheckObject
     */
    private function precheckCommon($bucket, $object, &$options, $isCheckObject = true)
    {
        if ($isCheckObject) {
            $this->precheckObject($object);
        }
        $this->precheckOptions($options);
        $this->precheckBucket($bucket);
    }

    /**
     * checks parameters
     *
     * @param array $options
     * @param string $param
     * @param string $funcName
     * @throws ObsException
     */
    private function precheckParam($options, $param, $funcName)
    {
        if (!isset($options[$param])) {
            throw new ObsException('The `' . $param . '` options is required in ' . $funcName . '().');
        }
    }

    /**
     * Checks md5
     *
     * @param array $options
     * @return bool|null
     */
    private function isCheckMD5($options)
    {
        return $this->getValue($options, self::OBS_CHECK_MD5, false, true, true);
    }

    /**
     * Gets value of the specified key from the options 
     *
     * @param array $options
     * @param string $key
     * @param string $default
     * @param bool $isCheckEmpty
     * @param bool $isCheckBool
     * @return bool|null
     */
    private function getValue($options, $key, $default = NULL, $isCheckEmpty = false, $isCheckBool = false)
    {
        $value = $default;
        if (isset($options[$key])) {
            if ($isCheckEmpty) {
                if (!empty($options[$key])) {
                    $value = $options[$key];
                }
            } else {
                $value = $options[$key];
            }
            unset($options[$key]);
        }
        if ($isCheckBool) {
            if ($value !== true && $value !== false) {
                $value = false;
            }
        }
        return $value;
    }

    /**
     * Gets mimetype
     *
     * @param string $object
     * @return string
     */
    private function getMimeType($object, $file = null)
    {
        if (!is_null($file)) {
            $type = MimeTypes::getMimetype($file);
            if (!is_null($type)) {
                return $type;
            }
        }

        $type = MimeTypes::getMimetype($object);
        if (!is_null($type)) {
            return $type;
        }

        return self::DEFAULT_CONTENT_TYPE;
    }

    /**
     * Validates and executes the request according to OBS API protocol.
     *
     * @param array $options
     * @return ResponseCore
     * @throws ObsException
     * @throws RequestCore_Exception
     */
    private function auth($options)
    {
        ObsUtil::validateOptions($options);
        //Validates bucket, not required for list_bucket
        $this->authPrecheckBucket($options);
        //Validates object
        $this->authPrecheckObject($options);
        //object name encoding must be UTF-8
        $this->authPrecheckObjectEncoding($options);
        //Validates ACL
        $this->authPrecheckAcl($options);
        // Should https or http be used?
        $scheme = $this->useSSL ? 'https://' : 'http://';
        // gets the host name. If the host name is public domain or private domain, form a third level domain by prefixing the bucket name on the domain name.
        $hostname = $this->generateHostname($options[self::OBS_BUCKET]);
        $string_to_sign = '';
        $headers = $this->generateHeaders($options, $hostname);
        $signable_query_string_params = $this->generateSignableQueryStringParam($options);
        $signable_query_string = ObsUtil::toQueryString($signable_query_string_params);
        $resource_uri = $this->generateResourceUri($options);
        //Generates the URL (add query parameters)
        $conjunction = '?';
        $non_signable_resource = '';
        if (isset($options[self::OBS_SUB_RESOURCE])) {
            $conjunction = '&';
        }
        if ($signable_query_string !== '') {
            $signable_query_string = $conjunction . $signable_query_string;
            $conjunction = '&';
        }
        $query_string = $this->generateQueryString($options);
        if ($query_string !== '') {
            $non_signable_resource .= $conjunction . $query_string;
            $conjunction = '&';
        }
        $this->requestUrl = $scheme . $hostname . $resource_uri . $signable_query_string . $non_signable_resource;

        //Creates the request
        $request = new RequestCore($this->requestUrl, $this->requestProxy);
        $request->set_useragent($this->generateUserAgent());
        // Streaming uploads
        if (isset($options[self::OBS_FILE_UPLOAD])) {
            if (is_resource($options[self::OBS_FILE_UPLOAD])) {
                $length = null;

                if (isset($options[self::OBS_CONTENT_LENGTH])) {
                    $length = $options[self::OBS_CONTENT_LENGTH];
                } elseif (isset($options[self::OBS_SEEK_TO])) {
                    $stats = fstat($options[self::OBS_FILE_UPLOAD]);
                    if ($stats && $stats[self::OBS_SIZE] >= 0) {
                        $length = $stats[self::OBS_SIZE] - (integer)$options[self::OBS_SEEK_TO];
                    }
                }
                $request->set_read_stream($options[self::OBS_FILE_UPLOAD], $length);
            } else {
                $request->set_read_file($options[self::OBS_FILE_UPLOAD]);
                $length = $request->read_stream_size;
                if (isset($options[self::OBS_CONTENT_LENGTH])) {
                    $length = $options[self::OBS_CONTENT_LENGTH];
                } elseif (isset($options[self::OBS_SEEK_TO]) && isset($length)) {
                    $length -= (integer)$options[self::OBS_SEEK_TO];
                }
                $request->set_read_stream_size($length);
            }
        }
        if (isset($options[self::OBS_SEEK_TO])) {
            $request->set_seek_position((integer)$options[self::OBS_SEEK_TO]);
        }
        if (isset($options[self::OBS_FILE_DOWNLOAD])) {
            if (is_resource($options[self::OBS_FILE_DOWNLOAD])) {
                $request->set_write_stream($options[self::OBS_FILE_DOWNLOAD]);
            } else {
                $request->set_write_file($options[self::OBS_FILE_DOWNLOAD]);
            }
        }

        if (isset($options[self::OBS_METHOD])) {
            $request->set_method($options[self::OBS_METHOD]);
            $string_to_sign .= $options[self::OBS_METHOD] . "\n";
        }

        if (isset($options[self::OBS_CONTENT])) {
            $request->set_body($options[self::OBS_CONTENT]);
            if ($headers[self::OBS_CONTENT_TYPE] === 'application/x-www-form-urlencoded') {
                $headers[self::OBS_CONTENT_TYPE] = 'application/octet-stream';
            }

            $headers[self::OBS_CONTENT_LENGTH] = strlen($options[self::OBS_CONTENT]);
            $headers[self::OBS_CONTENT_MD5] = ObsUtil::base64UrlSafeEncode(md5($options[self::OBS_CONTENT], true));
        }

        if (isset($options[self::OBS_CALLBACK])) {
            $headers[self::OBS_CALLBACK] = ObsUtil::base64UrlSafeEncode($options[self::OBS_CALLBACK]);
        }
        if (isset($options[self::OBS_CALLBACK_VAR])) {
            $headers[self::OBS_CALLBACK_VAR] = ObsUtil::base64UrlSafeEncode($options[self::OBS_CALLBACK_VAR]);
        }

        if (!isset($headers[self::OBS_ACCEPT_ENCODING])) {
            $headers[self::OBS_ACCEPT_ENCODING] = '';
        }

        uksort($headers, 'strnatcasecmp');

        foreach ($headers as $header_key => $header_value) {
            $header_value = str_replace(array("\r", "\n"), '', $header_value);
            if ($header_value !== '' || $header_key === self::OBS_ACCEPT_ENCODING) {
                $request->add_header($header_key, $header_value);
            }

            if (
                strtolower($header_key) === 'content-md5' ||
                strtolower($header_key) === 'content-type' ||
                strtolower($header_key) === 'date' ||
                (isset($options['self::OBS_PREAUTH']) && (integer)$options['self::OBS_PREAUTH'] > 0)
            ) {
                $string_to_sign .= $header_value . "\n";
            } elseif (substr(strtolower($header_key), 0, 6) === self::OBS_DEFAULT_PREFIX) {
                $string_to_sign .= strtolower($header_key) . ':' . $header_value . "\n";
            }
        }
        // Generates the signable_resource
        $signable_resource = $this->generateSignableResource($options);
        $string_to_sign .= rawurldecode($signable_resource) . urldecode($signable_query_string);

        // Sort the strings to be signed.
        $string_to_sign_ordered = $this->stringToSignSorted($string_to_sign);

        $signature = ObsUtil::base64UrlSafeEncode(hash_hmac('sha1', $string_to_sign_ordered, $this->accessKeySecret, true));
        $request->add_header('Authorization', 'OBS ' . $this->accessKeyId . ':' . $signature);

        if (isset($options[self::OBS_PREAUTH]) && (integer)$options[self::OBS_PREAUTH] > 0) {
            $signed_url = $this->requestUrl . $conjunction . self::OBS_URL_ACCESS_KEY_ID . '=' . rawurlencode($this->accessKeyId) . '&' . self::OBS_URL_EXPIRES . '=' . $options[self::OBS_PREAUTH] . '&' . self::OBS_URL_SIGNATURE . '=' . rawurlencode($signature);
            return $signed_url;
        } elseif (isset($options[self::OBS_PREAUTH])) {
            return $this->requestUrl;
        }

        if ($this->timeout !== 0) {
            $request->timeout = $this->timeout;
        }
        if ($this->connectTimeout !== 0) {
            $request->connect_timeout = $this->connectTimeout;
        }

        try {
            $request->send_request();
        } catch (RequestCore_Exception $e) {
            throw(new ObsException('RequestCoreException: ' . $e->getMessage()));
        }
        $response_header = $request->get_response_header();
        $response_header['obs-request-url'] = $this->requestUrl;
        $response_header['obs-redirects'] = $this->redirects;
        $response_header['obs-stringtosign'] = $string_to_sign;
        $response_header['obs-requestheaders'] = $request->request_headers;

        $data = new ResponseCore($response_header, $request->get_response_body(), $request->get_response_code());
        //retry if OBS Internal Error
        if ((integer)$request->get_response_code() === 500) {
            if ($this->redirects <= $this->maxRetries) {
                //Sets the sleep time betwen each retry.
                $delay = (integer)(pow(4, $this->redirects) * 100000);
                usleep($delay);
                $this->redirects++;
                $data = $this->auth($options);
            }
        }
        
        $this->redirects = 0;
        return $data;
    }

    /**
     * Sets the max retry count
     *
     * @param int $maxRetries
     * @return void
     */
    public function setMaxTries($maxRetries = 3)
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * Gets the max retry count
     *
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Enaable/disable STS in the URL. This is to determine the $sts value passed from constructor take effect or not.
     *
     * @param boolean $enable
     */
    public function setSignStsInUrl($enable)
    {
        $this->enableStsInUrl = $enable;
    }

    /**
     * @return boolean
     */
    public function isUseSSL()
    {
        return $this->useSSL;
    }

    /**
     * @param boolean $useSSL
     */
    public function setUseSSL($useSSL)
    {
        $this->useSSL = $useSSL;
    }

    /**
     * Validates bucket name--throw OssException if it's invalid
     *
     * @param $options
     * @throws ObsException
     */
    private function authPrecheckBucket($options)
    {
        if (!(('/' == $options[self::OBS_OBJECT]) && ('' == $options[self::OBS_BUCKET]) && ('GET' == $options[self::OBS_METHOD])) && !ObsUtil::validateBucket($options[self::OBS_BUCKET])) {
            throw new ObsException('"' . $options[self::OBS_BUCKET] . '"' . 'bucket name is invalid');
        }
    }

    /**
     *
     * Validates the object name--throw OssException if it's invalid.
     *
     * @param $options
     * @throws ObsException
     */
    private function authPrecheckObject($options)
    {
        if (isset($options[self::OBS_OBJECT]) && $options[self::OBS_OBJECT] === '/') {
            return;
        }

        if (isset($options[self::OBS_OBJECT]) && !ObsUtil::validateObject($options[self::OBS_OBJECT])) {
            throw new ObsException('"' . $options[self::OBS_OBJECT] . '"' . ' object name is invalid');
        }
    }

    /**
     * Checks the object's encoding. Convert it to UTF8 if it's in GBK or GB2312
     *
     * @param mixed $options parameter
     */
    private function authPrecheckObjectEncoding(&$options)
    {
        $tmp_object = $options[self::OBS_OBJECT];
        try {
            if (ObsUtil::isGb2312($options[self::OBS_OBJECT])) {
                $options[self::OBS_OBJECT] = iconv('GB2312', "UTF-8//IGNORE", $options[self::OBS_OBJECT]);
            } elseif (ObsUtil::checkChar($options[self::OBS_OBJECT], true)) {
                $options[self::OBS_OBJECT] = iconv('GBK', "UTF-8//IGNORE", $options[self::OBS_OBJECT]);
            }
        } catch (\Exception $e) {
            try {
                $tmp_object = iconv(mb_detect_encoding($tmp_object), "UTF-8", $tmp_object);
            } catch (\Exception $e) {
            }
        }
        $options[self::OBS_OBJECT] = $tmp_object;
    }

    /**
     * Checks if the ACL is one of the 3 predefined one. Throw OBSException if not.
     *
     * @param $options
     * @throws ObsException
     */
    private function authPrecheckAcl($options)
    {
        if (isset($options[self::OBS_HEADERS][self::OBS_ACL]) && !empty($options[self::OBS_HEADERS][self::OBS_ACL])) {
            if (!in_array(strtolower($options[self::OBS_HEADERS][self::OBS_ACL]), self::$OBS_ACL_TYPES)) {
                throw new ObsException($options[self::OBS_HEADERS][self::OBS_ACL] . ':' . 'acl is invalid(private,public-read,public-read-write)');
            }
        }
    }

    /**
     * Gets the host name for the current request.
     * It could be either a third level domain (prefixed by bucket name) or second level domain if it's CName or IP
     *
     * @param $bucket
     * @return string The host name without the protocol scheem (e.g. https://)
     */
    private function generateHostname($bucket)
    {
        if ($this->hostType === self::OBS_HOST_TYPE_IP) {
            $hostname = $this->hostname;
        } elseif ($this->hostType === self::OBS_HOST_TYPE_CNAME) {
            $hostname = $this->hostname;
        } else {
            // Private domain or public domain
            $hostname = ($bucket == '') ? $this->hostname : ($bucket . '.') . $this->hostname;
        }
        return $hostname;
    }

    /**
     * Gets the resource Uri in the current request
     *
     * @param $options
     * @return string return the resource uri.
     */
    private function generateResourceUri($options)
    {
        $resource_uri = "";

        // resource_uri + bucket
        if (isset($options[self::OBS_BUCKET]) && '' !== $options[self::OBS_BUCKET]) {
            if ($this->hostType === self::OBS_HOST_TYPE_IP) {
                $resource_uri = '/' . $options[self::OBS_BUCKET];
            }
        }

        // resource_uri + object
        if (isset($options[self::OBS_OBJECT]) && '/' !== $options[self::OBS_OBJECT]) {
            $resource_uri .= '/' . str_replace(array('%2F', '%25'), array('/', '%'), rawurlencode($options[self::OBS_OBJECT]));
        }

        // resource_uri + sub_resource
        $conjunction = '?';
        if (isset($options[self::OBS_SUB_RESOURCE])) {
            $resource_uri .= $conjunction . $options[self::OBS_SUB_RESOURCE];
        }
        return $resource_uri;
    }

    /**
     * Generates the signalbe query string parameters in array type
     *
     * @param array $options
     * @return array
     */
    private function generateSignableQueryStringParam($options)
    {
        $signableQueryStringParams = array();
        $signableList = array(
            self::OBS_PART_NUM,
            'response-content-type',
            'response-content-language',
            'response-cache-control',
            'response-content-encoding',
            'response-expires',
            'response-content-disposition',
            self::OBS_UPLOAD_ID,
            self::OBS_COMP,
            self::OBS_LIVE_CHANNEL_STATUS,
            self::OBS_LIVE_CHANNEL_START_TIME,
            self::OBS_LIVE_CHANNEL_END_TIME,
            self::OBS_PROCESS,
            self::OBS_POSITION,
            self::OBS_SYMLINK,
            self::OBS_RESTORE,
        );

        foreach ($signableList as $item) {
            if (isset($options[$item])) {
                $signableQueryStringParams[$item] = $options[$item];
            }
        }

        if ($this->enableStsInUrl && (!is_null($this->securityToken))) {
            $signableQueryStringParams["security-token"] = $this->securityToken;
        }

        return $signableQueryStringParams;
    }

    /**
     *  Generates the resource uri for signing
     *
     * @param mixed $options
     * @return string
     */
    private function generateSignableResource($options)
    {
        $signableResource = "";
        $signableResource .= '/';
        if (isset($options[self::OBS_BUCKET]) && '' !== $options[self::OBS_BUCKET]) {
            $signableResource .= $options[self::OBS_BUCKET];
            // if there's no object in options, adding a '/' if the host type is not IP.\
            if ($options[self::OBS_OBJECT] == '/') {
                if ($this->hostType !== self::OBS_HOST_TYPE_IP) {
                    $signableResource .= "/";
                }
            }
        }
        //signable_resource + object
        if (isset($options[self::OBS_OBJECT]) && '/' !== $options[self::OBS_OBJECT]) {
            $signableResource .= '/' . str_replace(array('%2F', '%25'), array('/', '%'), rawurlencode($options[self::OBS_OBJECT]));
        }
        if (isset($options[self::OBS_SUB_RESOURCE])) {
            $signableResource .= '?' . $options[self::OBS_SUB_RESOURCE];
        }
        return $signableResource;
    }

    /**
     * generates query string
     *
     * @param mixed $options
     * @return string
     */
    private function generateQueryString($options)
    {
        //query parameters
        $queryStringParams = array();
        if (isset($options[self::OBS_QUERY_STRING])) {
            $queryStringParams = array_merge($queryStringParams, $options[self::OBS_QUERY_STRING]);
        }
        return ObsUtil::toQueryString($queryStringParams);
    }

    private function stringToSignSorted($string_to_sign)
    {
        $queryStringSorted = '';
        $explodeResult = explode('?', $string_to_sign);
        $index = count($explodeResult);
        if ($index === 1)
            return $string_to_sign;

        $queryStringParams = explode('&', $explodeResult[$index - 1]);
        sort($queryStringParams);

        foreach($queryStringParams as $params)
        {
             $queryStringSorted .= $params . '&';    
        }

        $queryStringSorted = substr($queryStringSorted, 0, -1);

        return $explodeResult[0] . '?' . $queryStringSorted;
    }

    /**
     * Initialize headers
     *
     * @param mixed $options
     * @param string $hostname hostname
     * @return array
     */
    private function generateHeaders($options, $hostname)
    {
        $headers = array(
            self::OBS_CONTENT_MD5 => '',
            self::OBS_CONTENT_TYPE => isset($options[self::OBS_CONTENT_TYPE]) ? $options[self::OBS_CONTENT_TYPE] : self::DEFAULT_CONTENT_TYPE,
            self::OBS_DATE => isset($options[self::OBS_DATE]) ? $options[self::OBS_DATE] : gmdate('D, d M Y H:i:s \G\M\T'),
            self::OBS_HOST => $hostname,
        );
        if (isset($options[self::OBS_CONTENT_MD5])) {
            $headers[self::OBS_CONTENT_MD5] = $options[self::OBS_CONTENT_MD5];
        }

        //Add stsSecurityToken
        if ((!is_null($this->securityToken)) && (!$this->enableStsInUrl)) {
            $headers[self::OBS_SECURITY_TOKEN] = $this->securityToken;
        }
        //Merge HTTP headers
        if (isset($options[self::OBS_HEADERS])) {
            $headers = array_merge($headers, $options[self::OBS_HEADERS]);
        }
        return $headers;
    }

    /**
     * Generates UserAgent
     *
     * @return string
     */
    private function generateUserAgent()
    {
        return self::OBS_NAME . "/" . self::OBS_VERSION . " (" . php_uname('s') . "/" . php_uname('r') . "/" . php_uname('m') . ";" . PHP_VERSION . ")";
    }

    /**
     * Checks endpoint type and returns the endpoint without the protocol schema.
     * Figures out the domain's type (ip, cname or private/public domain).
     *
     * @param string $endpoint
     * @param boolean $isCName
     * @return string The domain name without the protocol schema.
     */
    private function checkEndpoint($endpoint, $isCName)
    {
        $ret_endpoint = null;
        if (strpos($endpoint, 'http://') === 0) {
            $ret_endpoint = substr($endpoint, strlen('http://'));
        } elseif (strpos($endpoint, 'https://') === 0) {
            $ret_endpoint = substr($endpoint, strlen('https://'));
            $this->useSSL = true;
        } else {
            $ret_endpoint = $endpoint;
        }

        if ($isCName) {
            $this->hostType = self::OBS_HOST_TYPE_CNAME;
        } elseif (ObsUtil::isIPFormat($ret_endpoint)) {
            $this->hostType = self::OBS_HOST_TYPE_IP;
        } else {
            $this->hostType = self::OBS_HOST_TYPE_NORMAL;
        }
        return $ret_endpoint;
    }

    /**
     * Check if all dependent extensions are installed correctly.
     * For now only "curl" is needed.
     * @throws ObsException
     */
    public static function checkEnv()
    {
        if (function_exists('get_loaded_extensions')) {
            //Test curl extension
            $enabled_extension = array("curl");
            $extensions = get_loaded_extensions();
            if ($extensions) {
                foreach ($enabled_extension as $item) {
                    if (!in_array($item, $extensions)) {
                        throw new ObsException("Extension {" . $item . "} is not installed or not enabled, please check your php env.");
                    }
                }
            } else {
                throw new ObsException("function get_loaded_extensions not found.");
            }
        } else {
            throw new ObsException('Function get_loaded_extensions has been disabled, please check php config.');
        }
    }

    /**
     * Sets the http's timeout (in seconds)
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Sets the http's connection timeout (in seconds)
     *
     * @param int $connectTimeout
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;
    }

    // Constants for Life cycle
    const OBS_LIFECYCLE_EXPIRATION = "Expiration";
    const OBS_LIFECYCLE_TIMING_DAYS = "Days";
    const OBS_LIFECYCLE_TIMING_DATE = "Date";
    //OBS Internal constants
    const OBS_BUCKET = 'bucket';
    const OBS_OBJECT = 'object';
    const OBS_HEADERS = ObsUtil::OBS_HEADERS;
    const OBS_METHOD = 'method';
    const OBS_QUERY = 'query';
    const OBS_BASENAME = 'basename';
    const OBS_MAX_KEYS = 'max-keys';
    const OBS_UPLOAD_ID = 'uploadId';
    const OBS_PART_NUM = 'partNumber';
    const OBS_COMP = 'comp';
    const OBS_LIVE_CHANNEL_STATUS = 'status';
    const OBS_LIVE_CHANNEL_START_TIME = 'startTime';
    const OBS_LIVE_CHANNEL_END_TIME = 'endTime';
    const OBS_POSITION = 'position';
    const OBS_MAX_KEYS_VALUE = 100;
    const OBS_MAX_OBJECT_GROUP_VALUE = ObsUtil::OBS_MAX_OBJECT_GROUP_VALUE;
    const OBS_MAX_PART_SIZE = ObsUtil::OBS_MAX_PART_SIZE;
    const OBS_MID_PART_SIZE = ObsUtil::OBS_MID_PART_SIZE;
    const OBS_MIN_PART_SIZE = ObsUtil::OBS_MIN_PART_SIZE;
    const OBS_FILE_SLICE_SIZE = 8192;
    const OBS_PREFIX = 'prefix';
    const OBS_DELIMITER = 'delimiter';
    const OBS_MARKER = 'marker';
    const OBS_ACCEPT_ENCODING = 'Accept-Encoding';
    const OBS_CONTENT_MD5 = 'Content-Md5';
    const OBS_SELF_CONTENT_MD5 = 'x-obs-meta-md5';
    const OBS_CONTENT_TYPE = 'Content-Type';
    const OBS_CONTENT_LENGTH = 'Content-Length';
    const OBS_IF_MODIFIED_SINCE = 'If-Modified-Since';
    const OBS_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
    const OBS_IF_MATCH = 'If-Match';
    const OBS_IF_NONE_MATCH = 'If-None-Match';
    const OBS_CACHE_CONTROL = 'Cache-Control';
    const OBS_EXPIRES = 'Expires';
    const OBS_PREAUTH = 'preauth';
    const OBS_CONTENT_COING = 'Content-Coding';
    const OBS_CONTENT_DISPOSTION = 'Content-Disposition';
    const OBS_RANGE = 'range';
    const OBS_ETAG = 'etag';
    const OBS_LAST_MODIFIED = 'lastmodified';
    const OS_CONTENT_RANGE = 'Content-Range';
    const OBS_CONTENT = ObsUtil::OBS_CONTENT;
    const OBS_BODY = 'body';
    const OBS_LENGTH = ObsUtil::OBS_LENGTH;
    const OBS_HOST = 'Host';
    const OBS_DATE = 'Date';
    const OBS_AUTHORIZATION = 'Authorization';
    const OBS_FILE_DOWNLOAD = 'fileDownload';
    const OBS_FILE_UPLOAD = 'fileUpload';
    const OBS_PART_SIZE = 'partSize';
    const OBS_SEEK_TO = 'seekTo';
    const OBS_SIZE = 'size';
    const OBS_QUERY_STRING = 'query_string';
    const OBS_SUB_RESOURCE = 'sub_resource';
    const OBS_DEFAULT_PREFIX = 'x-obs-';
    const OBS_CHECK_MD5 = 'checkmd5';
    const DEFAULT_CONTENT_TYPE = 'application/octet-stream';
    const OBS_SYMLINK_TARGET = 'x-obs-symlink-target';
    const OBS_SYMLINK = 'symlink';
    const OBS_HTTP_CODE = 'http_code';
    const OBS_REQUEST_ID = 'x-obs-request-id';
    const OBS_INFO = 'info';
    const OBS_STORAGE = 'storage';
    const OBS_RESTORE = 'restore';
    const OBS_STORAGE_STANDARD = 'Standard';
    const OBS_STORAGE_IA = 'IA';
    const OBS_STORAGE_ARCHIVE = 'Archive';

    //private URLs
    const OBS_URL_ACCESS_KEY_ID = 'OBSAccessKeyId';
    const OBS_URL_EXPIRES = 'Expires';
    const OBS_URL_SIGNATURE = 'Signature';
    //HTTP METHOD
    const OBS_HTTP_GET = 'GET';
    const OBS_HTTP_PUT = 'PUT';
    const OBS_HTTP_HEAD = 'HEAD';
    const OBS_HTTP_POST = 'POST';
    const OBS_HTTP_DELETE = 'DELETE';
    const OBS_HTTP_OPTIONS = 'OPTIONS';
    //Others
    const OBS_ACL = 'x-obs-acl';
    const OBS_OBJECT_ACL = 'x-obs-object-acl';
    const OBS_OBJECT_GROUP = 'x-obs-file-group';
    const OBS_MULTI_PART = 'uploads';
    const OBS_MULTI_DELETE = 'delete';
    const OBS_OBJECT_COPY_SOURCE = 'x-obs-copy-source';
    const OBS_OBJECT_COPY_SOURCE_RANGE = "x-obs-copy-source-range";
    const OBS_PROCESS = "x-obs-process";
    const OBS_CALLBACK = "x-obs-callback";
    const OBS_CALLBACK_VAR = "x-obs-callback-var";
    //Constants for STS SecurityToken
    const OBS_SECURITY_TOKEN = "x-obs-security-token";
    const OBS_ACL_TYPE_PRIVATE = 'private';
    const OBS_ACL_TYPE_PUBLIC_READ = 'public-read';
    const OBS_ACL_TYPE_PUBLIC_READ_WRITE = 'public-read-write';
    const OBS_ACL_TYPE_PUBLIC_READ_DELIVERED = 'public-read-delivered';
    const OBS_ACL_TYPE_PUBLIC_READ_WRITE_DELIVERED = 'public-read-write-delivered';
    const OBS_ENCODING_TYPE = "encoding-type";
    const OBS_ENCODING_TYPE_URL = "url";

    // Domain Types
    const OBS_HOST_TYPE_NORMAL = "normal";//http://bucket.obs-cn-hangzhou.huaweics.com/object
    const OBS_HOST_TYPE_IP = "ip";  //http://1.1.1.1/bucket/object
    const OBS_HOST_TYPE_SPECIAL = 'special'; //http://bucket.guizhou.gov/object
    const OBS_HOST_TYPE_CNAME = "cname";  //http://mydomain.com/object
    //OBS ACL array
    static $OBS_ACL_TYPES = array(
        self::OBS_ACL_TYPE_PRIVATE,
        self::OBS_ACL_TYPE_PUBLIC_READ,
        self::OBS_ACL_TYPE_PUBLIC_READ_WRITE
    );
    // OssClient version information
    const OBS_NAME = "huawei-sdk-php";
    const OBS_VERSION = "2.3.0";
    const OBS_BUILD = "20180105";
    const OBS_AUTHOR = "";
    const OBS_OPTIONS_ORIGIN = 'Origin';
    const OBS_OPTIONS_REQUEST_METHOD = 'Access-Control-Request-Method';
    const OBS_OPTIONS_REQUEST_HEADERS = 'Access-Control-Request-Headers';

    //use ssl flag
    private $useSSL = false;
    private $maxRetries = 3;
    private $redirects = 0;

    // user's domain type. It could be one of the four: OBS_HOST_TYPE_NORMAL, OBS_HOST_TYPE_IP, OBS_HOST_TYPE_SPECIAL, OBS_HOST_TYPE_CNAME
    private $hostType = self::OBS_HOST_TYPE_NORMAL;
    private $requestUrl;
    private $requestProxy = null;
    private $accessKeyId;
    private $accessKeySecret;
    private $hostname;
    private $securityToken;
    private $enableStsInUrl = false;
    private $timeout = 0;
    private $connectTimeout = 0;
}
