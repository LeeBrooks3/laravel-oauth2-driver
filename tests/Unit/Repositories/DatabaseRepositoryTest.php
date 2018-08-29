<?php

namespace LeeBrooks3\Laravel\OAuth2\Tests\Unit\Repositories;

use Illuminate\Contracts\Hashing\Hasher;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use LeeBrooks3\Laravel\OAuth2\Repositories\DatabaseRepository;
use LeeBrooks3\Laravel\OAuth2\Tests\Examples\Models\ExampleUser;
use LeeBrooks3\Repositories\ModelRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

class DatabaseRepositoryTest extends RepositoryTest
{
    /**
     * A mocked hasher instance.
     *
     * @var Hasher|MockObject
     */
    private $mockHasher;

    /**
     * Creates a mock model repository and hasher instance and the repository instance to test.
     */
    public function setUp()
    {
        parent::setUp();

        $model = new ExampleUser();
        $this->mockRepository = $this->createMock(ModelRepositoryInterface::class);
        $this->mockHasher = $this->createMock(Hasher::class);

        $this->repository = new DatabaseRepository($model, $this->mockRepository, $this->mockHasher);
    }

    /**
     * Tests that a users credentials can be validated.
     */
    public function testValidateCredentials()
    {
        $password = $this->faker->password;
        $user = new ExampleUser([
            'email' => $this->faker->email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
        $credentials = [
            'password' => $password,
        ];

        $this->mockHasher->expects($this->once())
            ->method('check')
            ->with($credentials['password'], $user->password)
            ->willReturn(true);

        $result = $this->repository->validateCredentials($user, $credentials);

        $this->assertTrue($result);
    }

    /**
     * Tests that a passport user entity can not be returned by credentials.
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
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        $this->mockRepository->expects($this->once())
            ->method('get')
            ->with([
                'email' => $user->email,
            ])
            ->willReturn([]);

        $result = $this->repository->getUserEntityByUserCredentials($email, $password, '', $mockClientEntity);

        $this->assertNull($result);
    }
}
