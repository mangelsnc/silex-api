<?php

namespace Album\Controller;

use Album\Album;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AlbumController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        //Get all the albums
        $controllers->get('/', function(Request $request, Application $app) {
            $sql = "SELECT * FROM albums";
            $albums = $app['db']->fetchAll($sql);

            return $app->json($albums, 200);
        });

        //Get single album
        $controllers->get('/{uid}', function(Request $request, Application $app, $uid) {
            $sql = "SELECT * FROM albums WHERE uid = ?";
            $album = $app['db']->fetchAssoc($sql, array($uid));

            if(!$album) {
                return new Response("Album not found", 404);
            }

            return $app->json($album, 200);
        });

        //Update an album
        $controllers->put('/{uid}', function(Request $request, Application $app, $uid) {
            $sql = "SELECT * FROM albums WHERE uid = ?";
            $album = $app['db']->fetchAssoc($sql, array($uid));

            if(!$album) {
                return new Response("Album not found", 404);   
            }

            $album = new Album();
            $album->setUID($uid);
            $album->setTitle($request->request->get('title') ? : null);
            $album->setAuthor($request->request->get('author') ? : null);
            $album->setYear($request->request->get('year') ? : null);
            $album->setGenre($request->request->get('genre') ? : null);
            
            //$sql = "UPDATE albums SET title = ?, author = ?, year = ?, genre = ? WHERE uid = ?";
            $app['db']->update('albums', array(
                    'title' => $album->getTitle(),
                    'author' => $album->getAuthor(),
                    'year' => $album->getYear(),
                    'genre' => $album->getGenre()
                ),array(
                    'uid' => $uid
                )
            );

            return $app->json($album->toArray(), 200); 
        });

        //Create new album
        $controllers->post('/', function(Request $request, Application $app) {
            $album = new Album();
            $album->setTitle($request->request->get('title') ? : null);
            $album->setAuthor($request->request->get('author') ? : null);
            $album->setYear($request->request->get('year') ? : null);
            $album->setGenre($request->request->get('genre') ? : null);
            
            $app['db']->insert('albums', array(
                'uid' => $album->getUID(),
                'title' => $album->getTitle(),
                'author' => $album->getAuthor(),
                'year' => $album->getYear(),
                'genre' => $album->getGenre()
            ));

            return $app->json($album->toArray(), 201);
        });

        //Delete an album
        $controllers->delete('/{uid}', function(Request $request, Application $app, $uid) {
            $sql = "SELECT * FROM albums WHERE uid = ?";
            $album = $app['db']->fetchAssoc($sql, array($uid));

            if(!$album) {
                return new Response("Album not found", 404);   
            }
            
            $app['db']->delete('albums', array(
                'uid' => $uid
            ));
            
            return $app->json(array(), 204);
        });        

        return $controllers;
    }
}