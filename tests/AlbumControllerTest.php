<?php

require __DIR__ . "/../vendor/autoload.php";

use Silex\WebTestCase;

class AlbumControllerTest extends WebTestCase
{
    public function createApplication()
    {
        require __DIR__ . '/../web/index.php';

        return $app;
    }

    public function testUnsuportedFormatReturns400()
    {
        $client = $this->createClient();
        
        $client->request(
            'GET', 
            '/albums', 
            array(), 
            array(), 
            array('Accept' => 'application/xml')
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }    

    public function testGetAlbumsCollectionShouldReturnMoreThanOneAlbum()
    {
        $client = $this->createClient();
        
        $client->request(
            'GET', 
            '/albums', 
            array(), 
            array(), 
            array('HTTP_ACCEPT' => 'application/json')
        );
        
        $items = count(json_decode($client->getResponse()->getContent()));
        
        $this->assertGreaterThan(1, $items);
    }

    public function testGetAnAlbumShouldReturns200()
    {
        $client = $this->createClient();
        
        $client->request(
            'GET', 
            '/albums/25ABCA6E', 
            array(), 
            array(), 
            array('HTTP_ACCEPT' => 'application/json')
        );
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetAnInexsistentAlbumShouldReturns404()
    {
        $client = $this->createClient();
        
        $client->request(
            'GET', 
            '/albums/fake-album', 
            array(), 
            array(), 
            array('HTTP_ACCEPT' => 'application/json')
        );
        
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    } 

    public function testPostAnAlbumShouldReturns201()
    {
        $client = $this->createClient();

        $client->request(
            'POST',
            '/albums/',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'title' => 'Sgt Peppers',
                'author' => 'The Beatles',
                'year' => '1978',
                'genre' => 'Pop'
            ))
        );
        $statusCode = $client->getResponse()->getStatusCode();

        $this->assertEquals(201, $statusCode);
    }

    public function testPutAnAlbumShouldReturns200()
    {
        $client = $this->createClient();

        $client->request(
            'POST',
            '/albums/',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'title' => 'Sgt Peppers',
                'author' => 'The Beatles',
                'year' => '1978',
                'genre' => 'Pop'
            ))
        );

        $album = json_decode($client->getResponse()->getContent());

        $client->request(
            'PUT',
            '/albums/' . $album->uid,
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'title' => 'Sgt Peppers',
                'author' => 'The Beatles',
                'year' => '1978',
                'genre' => 'Pop'
            ))
        );

        $statusCode = $client->getResponse()->getStatusCode();
        
        $this->assertEquals(200, $statusCode);
    }

    public function testPutAnInexistentAlbumShouldReturns404()
    {
        $client = $this->createClient();

        $client->request(
            'PUT',
            '/albums/inexistent',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'title' => 'Sgt Peppers',
                'author' => 'The Beatles'
            ))
        );

        $statusCode = $client->getResponse()->getStatusCode();
        
        $this->assertEquals(404, $statusCode);
    }

    public function testDeleteAnAlbumShouldReturns204()
    {
        $client = $this->createClient();

        $client->request(
            'POST',
            '/albums/',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'title' => 'Sgt Peppers',
                'author' => 'The Beatles',
                'year' => '1978',
                'genre' => 'Pop'
            ))
        );

        $album = json_decode($client->getResponse()->getContent());

        $client->request(
            'DELETE',
            '/albums/' . $album->uid,
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json')
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testTryToDeleteAnInexistentAlbumShouldReturns404()
    {
        $client = $this->createClient();

        $client->request(
            'DELETE',
            '/albums/inexistent',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json')
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());   
    }
}