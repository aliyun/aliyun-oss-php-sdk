<?php

namespace OSS\Core;

/**
 * Class OssException
 *
 * OssClient在使用的时候，所抛出的异常，用户在使用OssClient的时候，要Try住相关代码，
 * try的Exception应该是OssException，其中会得到相关异常原因
 *
 * @package OSS\Core
 */
class OssException extends \Exception
{

}
