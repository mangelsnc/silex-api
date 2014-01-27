<?php

namespace OAuth2Server\Controllers;

use Silex\Application;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;

class TokenController
{
    static public function addRoutes($routing)
    {
        $routing->post('/token', array(new self(), 'token'))->bind('grant');
    }

    public function token(Application $app)
    {
        $server = $app['oauth_server'];
        $response = $app['oauth_response'];
        $request = BridgeRequest::createFromRequest($app['request']);

        return $server->handleTokenRequest($request, $response);
    }
}