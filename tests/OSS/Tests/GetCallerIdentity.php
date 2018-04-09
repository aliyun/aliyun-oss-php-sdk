<?php

namespace OSS\Tests;


class GetCallerIdentity extends StsBase
{
    private $Action = "GetCallerIdentity";

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function getAttributes()
    {
        return     get_object_vars($this);
    }

    public function __construct()
    {
        parent::__construct();
    }
}
