<?php

namespace OAuth2Server;

use Silex\Application;
use Silex\ControllerProviderInterface;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Pdo;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;

class OAuth implements ControllerProviderInterface
{
    public function setup(Application $app)
    {
        $storage = new Pdo(array('dsn' => 'sqlite:'.__DIR__.'/../../../data/albums.db'));

        $grantTypes = array(
            'user_credentials'   => new UserCredentials($storage),
        );

        $server = new OAuth2Server(
                $storage, 
                array(
                    'enforce_state' => true, 
                    'allow_implicit' => true
                ), 
                $grantTypes
        );

        $app['oauth_server'] = $server;
        $app['oauth_response'] = new BridgeResponse();
    }

    public function connect(Application $app)
    {
        $this->setup($app);

        $routing = $app['controllers_factory'];

        Controllers\Authorize::addRoutes($routing);
        Controllers\Token::addRoutes($routing);

        return $routing;
    }
}
