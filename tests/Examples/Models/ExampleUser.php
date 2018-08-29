<?php

namespace LeeBrooks3\Laravel\OAuth2\Tests\Examples\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use LeeBrooks3\Laravel\Tests\Examples\Models\ExampleModel;

class ExampleUser extends ExampleModel implements AuthenticatableContract
{
    use Authenticatable;
}
