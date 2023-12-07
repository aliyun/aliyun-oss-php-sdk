<?php

namespace OSS\Crypto\Cipher\Aes\Context\CTR;

final class Context
{
    public $key;
    public $nonce;
    public $keyStream = '';
}
