<?php

namespace LeeBrooks3\Laravel\OAuth2\Tests\Unit\Providers;

use GuzzleHttp\Exception\BadResponseException;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Laravel\OAuth2\Providers\UserProvider;
use LeeBrooks3\Laravel\Tests\Examples\Models\ExampleUser;
use LeeBrooks3\Laravel\Tests\Unit\Providers\UserProviderTest as BaseUserProviderTest;
use LeeBrooks3\Repositories\ModelRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

class UserProviderTest extends BaseUserProviderTest
{
    /**
     * A mocked client instance.
     *
     * @var Client|MockObject
     */
    protected $mockClient;

    /**
     * Creates a mock model repository and client instance and the repository instance to test.
     */
    public function setUp()
    {
        parent::setUp();

        $model = new ExampleUser();
        $this->mockRepository = $this->createMock(ModelRepositoryInterface::class);
        $this->mockClient = $this->createMock(Client::class);

        $this->userProvider = new UserProvider($model, $this->mockRepository, $this->mockClient);
    }

    /**
     * Tests that a users credentials can be validated.
     */
    public function testValidateCredentials()
    {
        $user = new ExampleUser([
            'email' => $this->faker->email,
        ]);
        $credentials = [
            'password' => $this->faker->password,
        ];

        $this->mockClient->expects($this->once())
            ->method('getUserToken')
            ->with($user->email, $credentials['password']);

        $result = $this->userProvider->validateCredentials($user, $credentials);

        $this->assertTrue($result);
    }

    /**
     * Tests that a users credentials can fail validation.
     */
    public function testValidateCredentialsFailure()
    {
        /** @var \Exception $mockException */
        $mockException = $this->createMock(BadResponseException::class);
        $user = new ExampleUser([
            'email' => $this->faker->email,
        ]);
        $credentials = [
            'password' => $this->faker->password,
        ];

        $this->mockClient->expects($this->once())
            ->method('getUserToken')
            ->with($user->email, $credentials['password'])
            ->willThrowException($mockException);

        $result = $this->userProvider->validateCredentials($user, $credentials);

        $this->assertFalse($result);
    }
}
