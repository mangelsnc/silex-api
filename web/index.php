<?php

require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Album\Controller\AlbumController;

$app = new Silex\Application();
$app['db.path'] = __DIR__ . '/../data/';

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../data/albums.db',
    )
));


$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }

    if('application/json' != $request->headers->get("Accept")) {
        return new Response("Unsuported format", 400);
    }
});

$app->get("/", function(){
    return new Response();
});

$app->mount('/oauth', new OAuth2Server\OAuth());
$app->mount('/albums', new AlbumController());

$request = OAuth2\HttpFoundationBridge\Request::createFromGlobals();

$app->run($request);