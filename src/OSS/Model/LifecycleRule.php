<?php

namespace OSS\Model;


/**
 * Class LifecycleRule
 * @package OSS\Model
 *
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/PutBucketLifecycle.html
 */
class LifecycleRule
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $status;

    /**
     * @var LifecycleExpiration
     */
    private $expiration;
    /**
     * @var LifecycleAbortMultipartUpload
     */
    private $abortMultipartUpload;

    /**
     * @var LifecycleTag[]
     */
    private $tag;
    /**
     * @var LifecycleTransition[]
     */
    private $transition;

    /**
     * @var LifecycleNoncurrentVersionTransition[]
     */
    private $nonVersionTransition;

    /**
     * @var LifecycleFilter
     */
    private $filter;

    /**
     * @var LifecycleNoncurrentVersionExpiration
     */
    private $nonCurrentVersionExpiration;

    const STATUS_ENANLED = "Enabled";
    const STATUS_DISABLED = "Disabled";
    /**
     * @var LifecycleAction[]
     */
    private $actions;

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id Rule Id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get a file prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set a file prefix
     *
     * @param string $prefix The file prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get Lifecycle status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Lifecycle status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @return LifecycleAction[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param LifecycleAction[] $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * Get Lifecycle Expiration
     * @return LifecycleExpiration
     */
    public function getExpiration(){
        return $this->expiration;
    }

    /**
     * Set Lifecycle Expiration
     * @param $expiration LifecycleExpiration
     */
    public function setExpiration($expiration){
        $this->expiration = $expiration;
    }

    /**
     * Get Lifecycle Abort Multipart Upload
     * @return LifecycleAbortMultipartUpload
     */
    public function getAbortMultipartUpload(){
        return $this->abortMultipartUpload;
    }

    /**
     * Set Lifecycle Abort Multipart Upload
     * @param $abortMultipartUpload LifecycleAbortMultipartUpload
     */
    public function setAbortMultipartUpload($abortMultipartUpload){
        $this->abortMultipartUpload = $abortMultipartUpload;
    }

    /**
     * Get Lifecycle Tag
     * @return LifecycleTag[]
     */
    public function getTag(){
        return $this->tag;
    }

    /**
     * Set Lifecycle Tag
     * @param $tag LifecycleTag
     */
    public function addTag($tag){
        $this->tag[] = $tag;
    }

    /**
     * Get Lifecycle Transition
     * @return LifecycleTransition[]
     */
    public function getTransition(){
        return $this->transition;
    }

    /**
     * Set Lifecycle Transition
     * @param $transition LifecycleTransition
     */
    public function addTransition($transition){
        $this->transition[] = $transition;
    }


    /**
     * Get Lifecycle Non Current Version Transition
     * @return LifecycleNoncurrentVersionTransition[]
     */
    public function getNonCurrentVersionTransition(){
        return $this->nonVersionTransition;
    }

    /**
     * Set Lifecycle Non Current Version Transition
     * @param $nonVersionTransition LifecycleNonCurrentVersionTransition
     */
    public function addNonCurrentVersionTransition($nonVersionTransition){
        $this->nonVersionTransition[] = $nonVersionTransition;
    }

    /**
     * Get Lifecycle Noncurrent Version Expiration
     * @return LifecycleNoncurrentVersionExpiration
     */
    public function getNonCurrentVersionExpiration(){
        return $this->nonCurrentVersionExpiration;
    }

    /**
     * Set Lifecycle Non Current Version Expiration
     * @param $nonCurrentVersionExpiration LifecycleNoncurrentVersionExpiration
     */
    public function setNonCurrentVersionExpiration($nonCurrentVersionExpiration){
        $this->nonCurrentVersionExpiration = $nonCurrentVersionExpiration;
    }

    /**
     * Get Lifecycle Filter
     * @return LifecycleFilter
     */
    public function getFilter(){
        return $this->filter;
    }

    /**
     * Set Lifecycle Filter
     * @param $filter LifecycleFilter
     */
    public function setFilter($filter){
        $this->filter = $filter;
    }

    /**
     * LifecycleRule constructor.
     *
     * @param string $id rule Id
     * @param string $prefix File prefix
     * @param string $status Rule status, which has the following valid values: [self::LIFECYCLE_STATUS_ENABLED, self::LIFECYCLE_STATUS_DISABLED]
     * @param LifecycleAction[] $actions
     */
    public function __construct($id=null, $prefix=null, $status=null, $actions=null)
    {
        $this->id = $id;
        $this->prefix = $prefix;
        $this->status = $status;
        $this->actions = $actions;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        $xmlRule->addChild('ID', $this->id);
        $xmlRule->addChild('Prefix', $this->prefix);
        $xmlRule->addChild('Status', $this->status);
        if (isset($this->actions)){
            foreach ($this->actions as $action) {
                $action->appendToXml($xmlRule);
            }
        }

        if (isset($this->expiration)){
            $this->expiration->appendToXml($xmlRule);

        }
        if (isset($this->abortMultipartUpload)){
            $this->abortMultipartUpload->appendToXml($xmlRule);
        }

        if (isset($this->tag)){
            foreach ($this->tag as $tag){
                $tag->appendToXml($xmlRule);
            }
        }

        if (isset($this->transition)){
            foreach ($this->transition as $transition){
                $transition->appendToXml($xmlRule);
            }
        }

        if (isset($this->nonVersionTransition)){
            foreach ($this->nonVersionTransition as $nonVersionTransition){
                $nonVersionTransition->appendToXml($xmlRule);
            }
        }
        if (isset($this->nonCurrentVersionExpiration)){
            $this->nonCurrentVersionExpiration->appendToXml($xmlRule);
        }

        if (isset($this->filter)){
            $this->filter->appendToXml($xmlRule);
        }

    }


    /**
     * @param $xml \SimpleXMLElement
     */
    public function parseFromXml($xml)
    {
        if (isset($xml->ID)){
            $this->id = strval($xml->ID);
        }
        if (isset($xml->Prefix)){
            $this->prefix = strval($xml->Prefix);
        }
        if (isset($xml->Status)){
            $this->status = strval($xml->Status);
        }
        if (isset($xml->Expiration)){
            $this->parseExpiration($xml->Expiration);
        }
        if (isset($xml->Transition)){
            foreach ($xml->Transition as $transition){
                $this->parseTransition($transition);
            }
        }
        if (isset($xml->AbortMultipartUpload)){
            $this->parseAbortMultipartUpload($xml->AbortMultipartUpload);
        }
        if (isset($xml->Tag)){
            foreach ($xml->Tag as $tag){
                $this->parseTag($tag);
            }

        }
        if (isset($xml->NoncurrentVersionExpiration)){
            $this->parseNonCurrentVersionExpiration($xml->NoncurrentVersionExpiration);
        }
        if (isset($xml->NoncurrentVersionTransition)){
            foreach ($xml->NoncurrentVersionTransition as $transition){
                $this->parseNonCurrentVersionTransition($transition);
            }
        }
        if (isset($xml->Filter)){
            $this->parseFilter($xml->Filter);
        }
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    public function parseExpiration($xml)
    {
        if (!isset($xml->Days) && !isset($xml->Date) && !isset($xml->CreatedBeforeDate) && !isset($xml->ExpiredObjectDeleteMarker)) return;

        if (isset($xml->Days)){
            $days = strval($xml->Days);
        }
        if (isset($xml->Date)){
            $date = strval($xml->Date);
        }
        if (isset($xml->CreatedBeforeDate)){
            $createdBeforeDate = strval($xml->CreatedBeforeDate);
        }
        if (isset($xml->ExpiredObjectDeleteMarker)){
            $expiredObjectDeleteMarker = strval($xml->ExpiredObjectDeleteMarker);
        }

        $lifecycleExpiration = new LifecycleExpiration($days, $date, $createdBeforeDate, $expiredObjectDeleteMarker);
        $this->setExpiration($lifecycleExpiration);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    public function parseNonCurrentVersionExpiration($xml)
    {
        if (!isset($xml->NoncurrentDays)) return;

        if (isset($xml->NoncurrentDays)){
            $days = strval($xml->NoncurrentDays);
        }
        $expiration = new LifecycleNonCurrentVersionExpiration($days);
        $this->setNonCurrentVersionExpiration($expiration);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseTransition($xml)
    {
        if (!isset($xml->Days) && !isset($xml->CreatedBeforeDate) && !isset($xml->StorageClass) && !isset($xml->IsAccessTime) && !isset($xml->ReturnToStdWhenVisit) && !isset($xml->AllowSmallFile)) return;

        if (isset($xml->Days)){
            $days = strval($xml->Days);
        }
        if (isset($xml->CreatedBeforeDate)){
            $createdBeforeDate = strval($xml->CreatedBeforeDate);
        }
        if (isset($xml->StorageClass)){
            $storageClass = strval($xml->StorageClass);
        }
        if (isset($xml->IsAccessTime)){
            $isAccessTime = strval($xml->IsAccessTime);
        }
        if (isset($xml->ReturnToStdWhenVisit)){
            $returnToStdWhenVisit = strval($xml->ReturnToStdWhenVisit);
        }
        if (isset($xml->AllowSmallFile)){
            $allowSmallFile = strval($xml->AllowSmallFile);
        }

        $lifecycleTransition = new LifecycleTransition($days,$createdBeforeDate,$storageClass,$isAccessTime,$returnToStdWhenVisit,$allowSmallFile);
        $this->addTransition($lifecycleTransition);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseNonCurrentVersionTransition($xml)
    {
        if (!isset($xml->NoncurrentDays) && !isset($xml->StorageClass) && !isset($xml->IsAccessTime) && !isset($xml->ReturnToStdWhenVisit) && !isset($xml->AllowSmallFile)) return;

        if (isset($xml->NoncurrentDays)){
            $days = strval($xml->NoncurrentDays);
        }
        if (isset($xml->StorageClass)){
            $storageClass = strval($xml->StorageClass);
        }
        if (isset($xml->IsAccessTime)){
            $isAccessTime = strval($xml->IsAccessTime);
        }
        if (isset($xml->ReturnToStdWhenVisit)){
            $returnToStdWhenVisit = strval($xml->ReturnToStdWhenVisit);
        }
        if (isset($xml->AllowSmallFile)){
            $allowSmallFile = strval($xml->AllowSmallFile);
        }

        $lifecycleNonCurrentVersionTransition = new LifecycleNonCurrentVersionTransition($days,$storageClass,$isAccessTime,$returnToStdWhenVisit,$allowSmallFile);
        $this->addNonCurrentVersionTransition($lifecycleNonCurrentVersionTransition);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseAbortMultipartUpload($xml)
    {
        if (!isset($xml->Days) && !isset($xml->CreatedBeforeDate)) return;

        if (isset($xml->Days)){
            $days = strval($xml->Days);
        }
        if (isset($xml->CreatedBeforeDate)){
            $createdBeforeDate = strval($xml->CreatedBeforeDate);
        }

        $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload($days,$createdBeforeDate);
        $this->setAbortMultipartUpload($lifecycleAbortMultipartUpload);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseTag($xml)
    {
        if (!isset($xml->Key) && !isset($xml->Value)) return;

        if (isset($xml->Key)){
            $key = strval($xml->Key);
        }
        if (isset($xml->Value)){
            $value = strval($xml->Value);
        }

        $tag = new LifecycleTag($key,$value);
        $this->addTag($tag);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseFilter($xml)
    {
        if (isset($xml->Not)){
            foreach ($xml->Not as $not){
                $notObject[] = $this->parseNot($not);
            }
        }
        if (isset($xml->ObjectSizeGreaterThan)){
            $objectSizeGreaterThan = strval($xml->ObjectSizeGreaterThan);
        }
        if (isset($xml->ObjectSizeLessThan)){
            $objectSizeLessThan = strval($xml->ObjectSizeLessThan);
        }
        $lifecycleFilter = new LifecycleFilter($notObject,$objectSizeGreaterThan,$objectSizeLessThan);
        $this->setFilter($lifecycleFilter);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseNot($xml)
    {
        if (!isset($xml->Prefix) && !isset($xml->Tag)) return;
        if (isset($xml->Prefix)){
            $prefix = strval($xml->Prefix);
        }
        if (isset($xml->Tag)){
            $tag = $this->parseFilterTag($xml->Tag);
        }
        return new LifecycleNot($prefix,$tag);
    }

    /**
     * @param $xml \SimpleXMLElement
     */
    private function parseFilterTag($xml)
    {
        if (!isset($xml->Key) && !isset($xml->Value)) return;

        if (isset($xml->Key)){
            $key = strval($xml->Key);
        }
        if (isset($xml->Value)){
            $value = strval($xml->Value);
        }

        return new LifecycleTag($key,$value);
    }
}