<?php
namespace Oss\Credentials;

/**
 * Basic implementation of the OSS Credentials interface that allows callers to
 * pass in the OSS Access Key Id and OSS Secret Access Key in the constructor.
 */
class StaticCredentialsProvider implements CredentialsProvider
{

    /**
     * @var Credentials
     */
    private $credentials;
    /**
     * Constructs a new BasicOSSCredentials object, with the specified OSS
     * access key and OSS secret key
     *
     * @param string $key     OSS access key ID
     * @param string $secret  OSS secret access key
     * @param string $token   Security token to use
     */
    public function __construct($key, $secret, $token = null)
    {
        $this->credentials = new Credentials($key, $secret, $token);
    }


    /**
     * @return Credentials
     */
    public function getCredentials()
    {
        // TODO: Implement getCredentials() method.
        return $this->credentials;
    }
}
