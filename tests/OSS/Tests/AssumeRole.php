<?php
namespace OSS\Tests;


class AssumeRole extends StsBase
{
    private $Action = "AssumeRole";

    private $RoleArn;

    private $RoleSessionName;

    private  $Policy;

    private  $DurationSeconds;

    public function getAttributes()
    {
        return get_object_vars($this);
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function get($name)
    {
        return $this->$name;
    }
}
