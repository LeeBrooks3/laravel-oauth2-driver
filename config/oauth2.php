<?php

return [
    'client_id' => env('OAUTH2_CLIENT_ID'),
    'client_secret' => env('OAUTH2_CLIENT_SECRET'),
    'server_url' => env('OAUTH2_SERVER_URL'),
    'user_endpoint' => env('OAUTH2_USER_ENDPOINT', 'api/auth'),
    'token_endpoint' => env('OAUTH2_TOKEN_ENDPOINT', 'oauth/token'),
    'authorize_endpoint' => env('OAUTH2_AUTHORIZE_ENDPOINT', 'oauth/authorize'),
];
