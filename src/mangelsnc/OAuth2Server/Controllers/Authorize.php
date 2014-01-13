<?php

namespace OAuth2Server\Controllers;

use Silex\Application;

class Authorize
{
    static public function addRoutes($routing)
    {
        $routing->post('/authorize', array(new self(), 'authorizeFormSubmit'))->bind('authorize_post');
    }

    public function authorizeFormSubmit(Application $app)
    {

        $server = $app['oauth_server'];
        $response = $app['oauth_response'];
        $authorized = (bool) $app['request']->request->get('authorize');

        return $server->handleAuthorizeRequest($app['request'], $response, $authorized);
    }
}