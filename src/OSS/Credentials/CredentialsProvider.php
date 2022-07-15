<?php
namespace Oss\Credentials;

interface CredentialsProvider
{

    /**
     * @return Credentials
     */
    public function getCredentials();
}