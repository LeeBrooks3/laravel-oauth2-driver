<?php

namespace LeeBrooks3\Laravel\OAuth2\Tests\Unit\Http\Clients;

use Illuminate\Contracts\Config\Repository as Config;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Laravel\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends TestCase
{
    /**
     * @var Config|MockObject
     */
    private $mockConfig;

    /**
     * Creates a mock config repository instance.
     */
    public function setUp()
    {
        parent::setUp();

        $this->mockConfig = $this->createMock(Config::class);
    }

    /**
     * Tests that all the necessary properties are retrieved and set from config.
     */
    public function testConstructor()
    {
        $this->mockConfig->expects($this->any())
            ->method('get')
            ->withConsecutive(
                [
                    'oauth2.client_id',
                    null,
                ],
                [
                    'oauth2.client_secret',
                    null,
                ],
                [
                    'oauth2.server_url',
                    null,
                ],
                [
                    'oauth2.user_endpoint',
                    null,
                ],
                [
                    'oauth2.token_endpoint',
                    null,
                ],
                [
                    'oauth2.authorize_endpoint',
                    null,
                ]
            )
            ->willReturnMap([
                [
                    'oauth2.client_id',
                    null,
                    $this->faker->uuid,
                ],
                [
                    'oauth2.client_secret',
                    null,
                    $this->faker->uuid,
                ],
                [
                    'oauth2.server_url',
                    null,
                    $this->faker->url,
                ],
                [
                    'oauth2.user_endpoint',
                    null,
                    $this->faker->url,
                ],
                [
                    'oauth2.token_endpoint',
                    null,
                    $this->faker->url,
                ],
                [
                    'oauth2.authorize_endpoint',
                    null,
                    $this->faker->url,
                ],
            ]);

        $this->assertInstanceOf(Client::class, new Client($this->mockConfig));
    }
}
