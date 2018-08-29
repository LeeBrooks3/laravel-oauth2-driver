<?php

namespace LeeBrooks3\Laravel\OAuth2\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use LeeBrooks3\Repositories\ModelRepositoryInterface;

class DatabaseRepository extends Repository
{
    /**
     * A hasher instance.
     *
     * @var Hasher
     */
    private $hasher;

    /**
     * @param Authenticatable $model
     * @param ModelRepositoryInterface $repository
     * @param Hasher $hasher
     */
    public function __construct(Authenticatable $model, ModelRepositoryInterface $repository, Hasher $hasher)
    {
        parent::__construct($model, $repository);

        $this->hasher = $hasher;
    }

    /**
     * {@inheritdoc}
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }
}
