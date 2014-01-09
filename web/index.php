<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Album\Controller\AlbumController;

$app = new Silex\Application();
$app['db.path'] = __DIR__ . '/../data/';

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

$app->mount("/albums", new AlbumController());
$app->run();