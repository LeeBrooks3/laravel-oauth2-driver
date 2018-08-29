<?php

namespace LeeBrooks3\Laravel\OAuth2\Repositories;

use Illuminate\Contracts\Auth\UserProvider;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

interface RepositoryInterface extends UserProvider, UserRepositoryInterface
{
    //
}
