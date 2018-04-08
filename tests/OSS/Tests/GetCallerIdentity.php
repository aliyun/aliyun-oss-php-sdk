<?php
namespace OSS\Tests;


class GetCallerIdentity extends StsBase
{
    private $Action = "GetCallerIdentity";

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
