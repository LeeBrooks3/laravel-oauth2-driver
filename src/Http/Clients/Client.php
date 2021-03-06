<?php

namespace LeeBrooks3\Laravel\OAuth2\Http\Clients;

use Illuminate\Contracts\Config\Repository as Config;
use LeeBrooks3\Laravel\Models\UserInterface;
use LeeBrooks3\Models\ModelInterface;
use LeeBrooks3\OAuth2\Http\Clients\Client as BaseClient;
use LeeBrooks3\OAuth2\Models\AccessToken;

class Client extends BaseClient
{
    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $clientId = $config->get('oauth2.client_id');
        $clientSecret = $config->get('oauth2.client_secret');
        $serverUrl = $config->get('oauth2.server_url');
        $userEndpoint = $config->get('oauth2.user_endpoint');
        $tokenEndpoint = $config->get('oauth2.token_endpoint');
        $authorizeEndpoint = $config->get('oauth2.authorize_endpoint');

        $this->user = $config->get('auth.providers.users.model');

        parent::__construct($clientId, $clientSecret, $serverUrl, $userEndpoint, $tokenEndpoint, $authorizeEndpoint);
    }

    /**
     * {@inheritdoc}
     *
     * @param AccessToken $token
     * @return UserInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUser(AccessToken $token): ModelInterface
    {
        return parent::getUser($token);
    }
}
