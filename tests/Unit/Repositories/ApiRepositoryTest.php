<?php

namespace LeeBrooks3\Laravel\OAuth2\Tests\Unit\Repositories;

use GuzzleHttp\Exception\BadResponseException;
use Laravel\Passport\Bridge\User as PassportUser;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Laravel\OAuth2\Repositories\ApiRepository;
use LeeBrooks3\Laravel\OAuth2\Tests\Examples\Models\ExampleUser;
use LeeBrooks3\Repositories\ModelRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ApiRepositoryTest extends RepositoryTest
{
    /**
     * A mocked client instance.
     *
     * @var Client|MockObject
     */
    private $mockClient;

    /**
     * Creates a mock model repository and client instance and the repository instance to test.
     */
    public function setUp()
    {
        parent::setUp();

        $model = new ExampleUser();
        $this->mockRepository = $this->createMock(ModelRepositoryInterface::class);
        $this->mockClient = $this->createMock(Client::class);

        $this->repository = new ApiRepository($model, $this->mockRepository, $this->mockClient);
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


        $result = $this->repository->validateCredentials($user, $credentials);

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

        $result = $this->repository->validateCredentials($user, $credentials);

        $this->assertFalse($result);
    }

    /**
     * Tests that a passport user entity can be returned by credentials.
     */
    public function testGetUserEntityByCredentials()
    {
        /** @var ClientEntityInterface $mockClientEntity */
        $email = $this->faker->email;
        $password = $this->faker->password;
        $mockClientEntity = $this->createMock(ClientEntityInterface::class);
        $user = new ExampleUser([
            'id' => $this->faker->uuid,
            'email' => $email,
        ]);
        $credentials = [
            'password' => $password,
        ];

        $this->mockRepository->expects($this->once())
            ->method('get')
            ->with([
                'email' => $user->email,
            ])
            ->willReturn([
                $user,
            ]);

        $this->mockClient->expects($this->once())
            ->method('getUserToken')
            ->with($user->email, $credentials['password']);

        $result = $this->repository->getUserEntityByUserCredentials($email, $password, '', $mockClientEntity);

        $this->assertInstanceOf(PassportUser::class, $result);
    }
}
