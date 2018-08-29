<?php

namespace LeeBrooks3\Laravel\OAuth2\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Bridge\User as PassportUser;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use LeeBrooks3\Models\ModelInterface;
use LeeBrooks3\Repositories\ModelRepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    /**
     * A user model instance.
     *
     * @var Authenticatable
     */
    private $model;

    /**
     * A user repository instance.
     *
     * @var ModelRepositoryInterface
     */
    private $repository;

    /**
     * @param Authenticatable $model
     * @param ModelRepositoryInterface $repository
     */
    public function __construct(Authenticatable $model, ModelRepositoryInterface $repository)
    {
        $this->model = $model;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $identifier
     *
     * @return ModelInterface|Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->repository->find($identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $identifier
     * @param string $token
     *
     * @return ModelInterface|Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return array_first($this->repository->get([
            $this->model->getAuthIdentifierName() => $identifier,
            $this->model->getRememberTokenName() => $token,
        ]));
    }

    /**
     * {@inheritdoc}
     *
     * @param ModelInterface|Authenticatable $user
     * @param string $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token) : void
    {
        $this->repository->update($user, [
            $user->getRememberTokenName() => $token,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $credentials
     * @return ModelInterface|Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        return array_first($this->repository->get(array_except($credentials, 'password')));
    }

    /**
     * {@inheritdoc}
     *
     * @param $username
     * @param $password
     * @param $grantType
     * @param ClientEntityInterface $clientEntity
     * @return PassportUser|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        /** @var Authenticatable $user */
        $user = array_first($this->repository->get(['email' => $username]));

        if (!$user || !$this->validateCredentials($user, ['password' => $password])) {
            return null;
        }

        return new PassportUser($user->getAuthIdentifier());
    }
}
