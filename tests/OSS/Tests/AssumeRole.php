<?php
namespace OSS\Tests;


class AssumeRole extends StsBase
{
    public $Action = "AssumeRole";

    public $RoleArn;

    public $RoleSessionName;

    public  $Policy;

    public  $DurationSeconds;

}
