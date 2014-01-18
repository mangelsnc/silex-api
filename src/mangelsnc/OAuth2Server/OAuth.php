<?php

namespace OAuth2Server;

use Silex\Application;
use Silex\ControllerProviderInterface;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Pdo;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;

class OAuth implements ControllerProviderInterface
{
    public function setup(Application $app)
    {
        $storage = new Pdo(array('dsn' => 'sqlite:'.__DIR__.'/../../../data/albums.db'));

        $server = new OAuth2Server($storage);
        $server->addGrantType(new ClientCredentials($storage));
        $server->addGrantType(new AuthorizationCode($storage));

        $app['oauth_server'] = $server;
        $app['oauth_response'] = new BridgeResponse();
    }

    public function connect(Application $app)
    {
        $this->setup($app);

        $routing = $app['controllers_factory'];

        Controllers\AuthorizeController::addRoutes($routing);
        Controllers\TokenController::addRoutes($routing);

        return $routing;
    }
}
