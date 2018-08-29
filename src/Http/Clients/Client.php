<?php

namespace LeeBrooks3\Laravel\OAuth2\Http\Clients;

use Illuminate\Contracts\Config\Repository as Config;
use LeeBrooks3\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * The oauth2 client id.
     *
     * @var int
     */
    private $clientId;

    /**
     * The oauth2 client secret.
     *
     * @var string
     */
    private $clientSecret;

    /**
     * The oauth2 server url
     *
     * @var string
     */
    private $serverUrl;

    /**
     * The oauth2 token endpoint.
     *
     * @var string
     */
    private $tokenEndpoint;

    /**
     * The oauth2 authorize endpoint.
     *
     * @var string
     */
    private $authorizeEndpoint;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->clientId = $config->get('oauth2.providers.api.client_id');
        $this->clientSecret = $config->get('oauth2.providers.api.client_secret');
        $this->serverUrl = $config->get('oauth2.providers.api.server_url');
        $this->tokenEndpoint = $config->get('oauth2.providers.api.token_endpoint');
        $this->authorizeEndpoint = $config->get('oauth2.providers.api.authorize_endpoint');

        parent::__construct([
            'base_uri' => $this->serverUrl . '/',
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Returns a user token via the given credentials.
     *
     * @param string $username
     * @param string $password
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserToken(string $username, string $password) : array
    {
        $response = $this->post($this->tokenEndpoint, [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $username,
                'password' => $password,
            ],
        ]);

        $json = $response->getBody()->getContents();
        $data = \GuzzleHttp\json_decode($json, true);

        return $data;
    }

    /**
     * Returns an authentication token.
     *
     * @param string $redirectUri
     * @param string $code
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAuthToken(string $redirectUri, string $code) : array
    {
        $response = $this->post($this->tokenEndpoint, [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ],
        ]);

        $json = $response->getBody()->getContents();
        $data = \GuzzleHttp\json_decode($json, true);

        return $data;
    }

    /**
     * Returns the url to redirect to authenticate.
     *
     * @param string $redirectUri
     * @return string
     */
    public function getAuthUrl(string $redirectUri) : string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => '',
        ]);

        return "{$this->serverUrl}/{$this->authorizeEndpoint}?{$query}";
    }
}
