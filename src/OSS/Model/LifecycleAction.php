<?php

namespace OSS\Model;

/**
 * Class LifecycleAction
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/PutBucketLifecycle.html
 */
class LifecycleAction
{
    /**
     * LifecycleAction constructor.
     * @param string $action
     * @param string $timeSpec
     * @param string $timeValue
     */
    public function __construct($action, $timeSpec, $timeValue)
    {
        $this->action = $action;
        $this->timeSpec = $timeSpec;
        $this->timeValue = $timeValue;
    }

    /**
     * @return LifecycleAction
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getTimeSpec()
    {
        return $this->timeSpec;
    }

    /**
     * @param string $timeSpec
     */
    public function setTimeSpec($timeSpec)
    {
        $this->timeSpec = $timeSpec;
    }

    /**
     * @return string
     */
    public function getTimeValue()
    {
        return $this->timeValue;
    }

    /**
     * @param string $timeValue
     */
    public function setTimeValue($timeValue)
    {
        $this->timeValue = $timeValue;
    }

    /**
     * Use appendToXml to insert actions into xml.
     *
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        switch ($this->action) {
            case 'Tag':
                $tag = $xmlRule->Tag;
                if($tag){
                    $number = count($tag);
                    $currentTag = $xmlRule->Tag[$number-1];
                    if($currentTag->Key && $currentTag->Value){
                        $xmlAction = $xmlRule->addChild($this->action);
                        $xmlAction->addChild($this->timeSpec, $this->timeValue);
                    }else{
                        $currentTag->addChild($this->timeSpec, $this->timeValue);
                    }
                }else{
                    $xmlAction = $xmlRule->addChild($this->action);
                    $xmlAction->addChild($this->timeSpec, $this->timeValue);
                }
                # code...
                break;
            case 'Transition':
                $transition = $xmlRule->Transition;
                if($transition){
                    $number = count($transition);
                    $currentTransition = $xmlRule->Transition[$number-1];
                    if($currentTransition->Days && $currentTransition->StorageClass){
                        $xmlAction = $xmlRule->addChild($this->action);
                        $xmlAction->addChild($this->timeSpec, $this->timeValue);
                    }else{
                        $currentTransition->addChild($this->timeSpec, $this->timeValue);
                    }
                }else{
                    $xmlAction = $xmlRule->addChild($this->action);
                    $xmlAction->addChild($this->timeSpec, $this->timeValue);
                }
                # code...
                break;
            case 'NoncurrentVersionTransition':
                if ($xmlRule->NoncurrentVersionTransition) {
                    $xmlRule->NoncurrentVersionTransition->addChild($this->timeSpec, $this->timeValue);
                } else {
                    $xmlAction = $xmlRule->addChild($this->action);
                    $xmlAction->addChild($this->timeSpec, $this->timeValue);
                }
                break;
            default:
                $xmlAction = $xmlRule->addChild($this->action);
                $xmlAction->addChild($this->timeSpec, $this->timeValue);

        }
    }

    private $action;
    private $timeSpec;
    private $timeValue;

}