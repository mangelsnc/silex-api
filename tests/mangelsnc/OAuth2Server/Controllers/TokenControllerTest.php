<?php

require __DIR__ . "/../../../../vendor/autoload.php";

use Silex\WebTestCase;

class TokenControllerTest extends WebTestCase
{
    public function createApplication()
    {
        require __DIR__ . '/../../../../web/index.php';

        return $app;
    }

    public function testGetTokenWithCorrectCredentialsReturn200()
    {
        $client = $this->createClient();
        
        $client->request(
            'POST', //Method
            '/oauth/token',  //URI
            array(), //Parameters 
            array(), //Files
            array( //Headers
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/x-www-form-urlencoded'
            ),
            'grant_type=client_credentials&client_id=testclient&client_secret=testpass' //Body
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

}