<?php 
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClubControllerTest extends WebTestCase
{
    public function testListJugadoresFromClub()
    {
        $client = static::createClient();
        $client->request('GET', '/api/club/1/jugadores');
        
        // Habilitar el modo de depuración
        if ($client->getResponse()->getStatusCode() === Response::HTTP_INTERNAL_SERVER_ERROR) {
            echo $client->getResponse()->getContent();
        }

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    
    public function testListJugadoresFromClubNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/club/999/jugadores');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Club no encontrado', $client->getResponse()->getContent());
    }

    public function testAddEntrenadorToClub()
    {
        $client = static::createClient();
        $client->request('POST', '/api/club/1/add-entrenador', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'id_entrenador' => 1,
            'salario' => 50000
        ]));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Entrenador agregado al Club', $client->getResponse()->getContent());
    }

    public function testAddEntrenadorToClubNotFound()
    {
        $client = static::createClient();
        $client->request('POST', '/api/club/999/add-entrenador', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'id_entrenador' => 1,
            'salario' => 50000
        ]));

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Club no encontrado', $client->getResponse()->getContent());
    }
    
}