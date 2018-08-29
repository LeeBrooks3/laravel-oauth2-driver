<?php

namespace LeeBrooks3\Laravel\OAuth2\Providers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Laravel\Providers\UserProvider as BaseUserProvider;
use LeeBrooks3\Repositories\ModelRepositoryInterface;

class UserProvider extends BaseUserProvider
{
    /**
     * A client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * @param Authenticatable $model
     * @param ModelRepositoryInterface $repository
     * @param Client $client
     */
    public function __construct(
        Authenticatable $model,
        ModelRepositoryInterface $repository,
        Client $client
    ) {
        parent::__construct($model, $repository);

        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials) : bool
    {
        try {
            $this->client->getUserToken($user->email, $credentials['password']);

            return true;
        } catch (GuzzleException $e) {
            return false;
        }
    }
}
