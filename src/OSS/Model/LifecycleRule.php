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
	 * @return bool
	 */
    public function hasTags(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Tag'){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Expiration' && $action->getTimeSpec() == 'Days'){
				return true;
			}
		}
		return false;
	}
	
	
	public function hasAbortMultipartUpload(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'AbortMultipartUpload'){
				return true;
			}
		}
		return false;
	}
	
	public function hasAbortMultipartUploadExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'AbortMultipartUpload' && $action->getTimeSpec() == 'Days'){
				return true;
			}
		}
		return false;
	}
	
	public function hasAbortMultipartUploadCreatedBeforeDate(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'AbortMultipartUpload' && $action->getTimeSpec() == 'CreatedBeforeDate'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasCreatedBeforeDate(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Expiration' && $action->getTimeSpec() == 'CreatedBeforeDate'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasExpiredDeleteMarker(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Expiration' && $action->getTimeSpec() == 'ExpiredObjectDeleteMarker'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasStorageTransition(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Transition'){
				return true;
			}
		}
		return false;
	}
	
	
	
	/**
	 * @return bool
	 */
	public function hasStorageTransitionExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Transition' && $action->getTimeSpec() == 'Days'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasStorageTransitionCreatedBeforeDate(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Transition' && $action->getTimeSpec() == 'CreatedBeforeDate'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasNoncurrentVersionStorageTransitions(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'NoncurrentVersionTransition'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return bool
	 */
	public function hasNoncurrentVersionExpiration(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'NoncurrentVersionExpiration'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @return string
	 */
	public function getTags(){
    	$str = '';
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Tag'){
				switch ($action->getTimeSpec()){
					case 'Key':
						$str .= '{'.$action->getTimeValue();
						break;
					case 'Value':
						$str .= '='.$action->getTimeValue().'}';
						break;
				}
			}
		}
		return $str;
	}
	
	/**
	 * @return string
	 */
	public function getExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Expiration' && $action->getTimeSpec() == 'Days'){
				return $action->getTimeValue();
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getCreatedBeforeDate(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Expiration' && $action->getTimeSpec() == 'CreatedBeforeDate'){
				return $action->getTimeValue();
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getAbortMultipartUploadExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'AbortMultipartUpload' && $action->getTimeSpec() == 'Days'){
				return $action->getTimeValue();
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getAbortMultipartUploadCreatedBeforeDate(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'AbortMultipartUpload' && $action->getTimeSpec() == 'CreatedBeforeDate'){
				return $action->getTimeValue();
			}
		}
	}
	
	
	
	public function getStorageTransitionExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Transition' &&  $action->getTimeSpec() == 'Days'){
				return $action->getTimeValue();
			}
		}
	}
	
	public function getStorageTransitionCreatedBeforeDate(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Transition' &&  $action->getTimeSpec() == 'CreatedBeforeDate'){
				return $action->getTimeValue();
			}
		}
	}
	
	public function getStorageTransitionStorageClass(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Transition' &&  $action->getTimeSpec() == 'StorageClass'){
				return $action->getTimeValue();
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getExpiredDeleteMarker(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'Expiration' && $action->getTimeSpec() == 'ExpiredObjectDeleteMarker'){
				return $action->getTimeValue();
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function getNoncurrentVersionStorageTransitionsNoncurrentDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'NoncurrentVersionTransition' && $action->getTimeSpec() == 'NoncurrentDays'){
				return  $action->getTimeValue();
			}
			
		}
	}
	
	/**
	 * @return string
	 */
	public function getNoncurrentVersionStorageTransitionsStorageClass(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'NoncurrentVersionTransition' && $action->getTimeSpec() == 'StorageClass'){
				return  $action->getTimeValue();
			}
			
		}
	}
	
	/**
	 * @return string
	 */
	public function getNoncurrentVersionExpirationDays(){
		foreach ($this->actions as $action) {
			if($action->getAction() == 'NoncurrentVersionExpiration'){
				switch ($action->getTimeSpec()){
					case 'NoncurrentDays':
						return $action->getTimeValue();
				}
			}
		}
	}


    /**
     * LifecycleRule constructor.
     *
     * @param string $id rule Id
     * @param string $prefix File prefix
     * @param string $status Rule status, which has the following valid values: [self::LIFECYCLE_STATUS_ENABLED, self::LIFECYCLE_STATUS_DISABLED]
     * @param LifecycleAction[] $actions
     */
    public function __construct($id, $prefix, $status, $actions)
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
        foreach ($this->actions as $action) {
            $action->appendToXml($xmlRule);
        }
    }

    private $id;
    private $prefix;
    private $status;
    private $actions = array();

    const LIFECYCLE_STATUS_ENABLED = 'Enabled';
    const LIFECYCLE_STATUS_DISABLED = 'Disabled';
}