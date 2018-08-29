<?php

namespace LeeBrooks3\Laravel\OAuth2\Providers;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use League\OAuth2\Server\Grant\PasswordGrant;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Laravel\OAuth2\Repositories\ApiRepository;
use LeeBrooks3\Laravel\OAuth2\Repositories\DatabaseRepository as DatabaseRepository;
use LeeBrooks3\Models\ModelInterface;
use LeeBrooks3\Repositories\ModelRepositoryInterface;

class ServiceProvider extends PassportServiceProvider
{
    /**
     * An auth manager instance.
     *
     * @var AuthManager
     */
    private $auth;

    /**
     * A config repository instance.
     *
     * @var Config
     */
    private $config;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function boot() : void
    {
        parent::boot();

        $configPath = $this->app->make('path.config');

        $this->mergeConfigFrom(__DIR__ .'/../../config/oauth2.php', 'oauth2');

        $this->publishes([
            __DIR__ .'/../../config/oauth2.php' => $configPath . DIRECTORY_SEPARATOR .'oauth2.php',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function register() : void
    {
        $this->auth = $this->app->make(AuthManager::class);
        $this->config = $this->app->make(Config::class);

        $this->registerUserProviders();

        parent::register();
    }

    /**
     * {@inheritdoc}
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    protected function makePasswordGrant() : PasswordGrant
    {
        $grant = new PasswordGrant(
            $this->makeDatabaseRepository(),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    /**
     * Makes a database repository instance.
     *
     * @return DatabaseRepository
     */
    protected function makeDatabaseRepository() : DatabaseRepository
    {
        $config = $this->config->get('auth.providers.users');

        /**
         * @var ModelInterface $model
         * @var ModelRepositoryInterface $repository
         */
        $model = $this->app->make($config['model']);
        $repository = $this->app->make($config['repository']);
        $hasher = $this->app->make(Hasher::class);

        return new DatabaseRepository($model, $repository, $hasher);
    }

    /**
     * Makes an API repository instance.
     *
     * @return ApiRepository
     */
    protected function makeApiRepository() : ApiRepository
    {
        $config = $this->config->get('auth.providers.users');

        /**
         * @var ModelInterface $model
         * @var ModelRepositoryInterface $repository
         * @var Client $client
         */
        $model = $this->app->make($config['model']);
        $repository = $this->app->make($config['repository']);
        $client = $this->app->make(Client::class);

        return new ApiRepository($model, $repository, $client);
    }

    /**
     * Registers the user provider.
     *
     * @return void
     */
    private function registerUserProviders() : void
    {
        $this->auth->provider('oauth2_database', function () {
            return $this->makeDatabaseRepository();
        });

        $this->auth->provider('oauth2_api', function () {
            return $this->makeApiRepository();
        });
    }
}
