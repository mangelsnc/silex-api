<?php

require __DIR__ . "/../vendor/autoload.php";

use Silex\WebTestCase;

class AlbumControllerTest extends WebTestCase
{
    public function createApplication()
    {
        require __DIR__ . '/../web/index.php';
        file_put_contents(
            __DIR__ . '/../data/test.json', 
            "{title: 'Sgt Peppers', author: 'The Beatles', year: 1978, genre: 'Pop'}");

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
            '/albums/test', 
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

        $album = json_decode($client->getResponse()->getContent());
        unlink(__DIR__ . '/../data/' . $album->uid . '.json');

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testPutAnAlbumShouldReturns200()
    {
        $client = $this->createClient();

        $client->request(
            'PUT',
            '/albums/test',
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

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteAnAlbumShouldReturns204()
    {
        $client = $this->createClient();

        $client->request(
            'DELETE',
            '/albums/test',
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