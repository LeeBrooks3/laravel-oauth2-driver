<?php

namespace LeeBrooks3\Laravel\OAuth2\Tests\Unit\Http\Clients;

use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Config\Repository as Config;
use LeeBrooks3\Laravel\OAuth2\Http\Clients\Client;
use LeeBrooks3\Laravel\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends TestCase
{
    /**
     * A mocked config repository instance.
     *
     * @var Config|MockObject
     */
    private $mockConfig;

    /**
     * The client instance.
     *
     * @var Client|MockObject
     */
    private $mockClient;

    /**
     * The oauth2 client id.
     *
     * @var int
     */
    private $clientId;

    /**
     * The oauth2 client secret.
     *
     * @var string
     */
    private $clientSecret;

    /**
     * The oauth2 server url
     *
     * @var string
     */
    private $serverUrl;

    /**
     * The oauth2 token endpoint.
     *
     * @var string
     */
    private $tokenEndpoint;

    /**
     * The oauth2 authorize endpoint.
     *
     * @var string
     */
    private $authorizeEndpoint;

    /**
     * Creates a mock config repository instance and the partially mocked client instance.
     */
    public function setUp()
    {
        parent::setUp();

        $this->mockConfig = $this->createMock(Config::class);

        $this->mockConfig->expects($this->any())
            ->method('get')
            ->willReturnMap([
                [
                    'oauth2.providers.api.client_id',
                    null,
                    $this->clientId = $this->faker->uuid,
                ],
                [
                    'oauth2.providers.api.client_secret',
                    null,
                    $this->clientSecret = $this->faker->uuid,
                ],
                [
                    'oauth2.providers.api.server_url',
                    null,
                    $this->serverUrl = $this->faker->url,
                ],
                [
                    'oauth2.providers.api.token_endpoint',
                    null,
                    $this->tokenEndpoint = $this->faker->url,
                ],
                [
                    'oauth2.providers.api.authorize_endpoint',
                    null,
                    $this->authorizeEndpoint = $this->faker->url,
                ],
            ]);

        $this->mockClient = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->mockConfig])
            ->setMethods(['post'])
            ->getMock();
    }

    /**
     * Tests that a post request is made to get a user token.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetUserToken()
    {
        $username = $this->faker->email;
        $password = $this->faker->password;

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with($this->tokenEndpoint, [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'username' => $username,
                    'password' => $password,
                ],
            ])
            ->willReturn(new Response(200, [], \GuzzleHttp\json_encode([])));

        $this->mockClient->getUserToken($username, $password);
    }

    /**
     * Tests that a post request is made to get an auth token.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetAuthToken()
    {
        $redirectUri = $this->faker->url;
        $code = $this->faker->uuid;

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with($this->tokenEndpoint, [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $redirectUri,
                    'code' => $code,
                ],
            ])
            ->willReturn(new Response(200, [], \GuzzleHttp\json_encode([])));

        $this->mockClient->getAuthToken($redirectUri, $code);
    }

    /**
     * Tests that the url to redirect to authenticate is returned.
     */
    public function testGetAuthUrl()
    {
        $redirectUri = $this->faker->url;

        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => '',
        ]);

        $result = $this->mockClient->getAuthUrl($redirectUri);

        $this->assertEquals("{$this->serverUrl}/{$this->authorizeEndpoint}?{$query}", $result);
    }
}
