<?php

namespace Album\Controller;

use Album\Album;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AlbumController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        //Get all the albums
        $controllers->get('/', function(Request $request, Application $app) {
            $albums = array();

            $db = scandir($app['db.path']);
            array_shift($db);
            array_shift($db);

            foreach($db as $album) {
                $albums[] = json_decode(file_get_contents($app['db.path'] . $album));
            }

            return $app->json($albums, 200);
        });

        //Get single album
        $controllers->get('/{uid}', function(Request $request, Application $app, $uid) {
            if(!file_exists($app['db.path'] . $uid . '.json')) {
                $app->abort(404, 'Album does not exist');    
            }

            $album = json_decode(file_get_contents($app['db.path'] . $uid . '.json'));

            return $app->json($album, 200);
        });

        //Update an album
        $controllers->put('/{uid}', function(Request $request, Application $app, $uid) {
            if(!file_exists($app['db.path'] . $uid . '.json')) {
                $app->abort(404, 'Album does not exist');    
            }

            $album = new Album();
            $album->setUID($request->request->get('uid'));
            $album->setTitle($request->request->get('title'));
            $album->setAuthor($request->request->get('author'));
            $album->setYear($request->request->get('year'));
            $album->setGenre($request->request->get('genre'));   
            
            file_put_contents($app['db.path'] . $uid . '.json', $album->toJSON());

            return $app->json($album->toArray(), 200); 
        });

        //Create new album
        $controllers->post('/new', function(Request $request, Application $app) {
            $album = new Album();
            $album->setTitle($request->request->get('title'));
            $album->setAuthor($request->request->get('author'));
            $album->setYear($request->request->get('year'));
            $album->setGenre($request->request->get('genre'));   
            
            file_put_contents($app['db.path'] . $album->getUID() . '.json', $album->toJSON());

            return $app->json($album->toArray(), 201);
        });

        //Delete an album
        $controllers->delete('/{uid}', function(Request $request, Application $app, $uid) {
            if(!file_exists($app['db.path'] . $uid . '.json')) {
                $app->abort(404, 'Album does not exist');    
            }
            
            unlink($app['db.path'] . $uid . '.json');
            
            return $app->json(array(), 204);
        });        

        return $controllers;
    }
}