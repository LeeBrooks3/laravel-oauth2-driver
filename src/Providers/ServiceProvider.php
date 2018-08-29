<?php

namespace LeeBrooks3\Laravel\OAuth2\Providers;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Models\ModelInterface;
use LeeBrooks3\Repositories\ModelRepositoryInterface;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Merges/publishes the oauth2 config file.
     *
     * @return void
     */
    public function boot() : void
    {
        $configPath = $this->app->make('path.config');

        $this->mergeConfigFrom(__DIR__ .'/../../config/oauth2.php', 'oauth2');

        $this->publishes([
            __DIR__ .'/../../config/oauth2.php' => $configPath . DIRECTORY_SEPARATOR .'oauth2.php',
        ]);
    }

    /**
     * Registers the api repository user provider.
     *
     * @return void
     */
    public function register() : void
    {
        /** @var AuthManager $auth */
        $auth = $this->app->make(AuthManager::class);

        $auth->provider('api_repository', function (Application $app, array $config) {
            /**
             * @var ModelInterface $model
             * @var ModelRepositoryInterface $repository
             * @var Client $client
             */
            $model = $app->make($config['model']);
            $repository = $app->make($config['repository']);
            $client = $app->make(Client::class);

            return new UserProvider($model, $repository, $client);
        });
    }
}
