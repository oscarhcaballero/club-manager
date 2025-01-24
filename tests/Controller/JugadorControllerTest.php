<?php 
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JugadorControllerTest extends WebTestCase
{
    public function testListJugadores()
    {
        $client = static::createClient();
        $client->request('GET', '/api/jugadores');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateJugador()
    {
        $client = static::createClient();
        $client->request('POST', '/api/create-jugador', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'nombre' => 'Nuevo Jugador',
            'email' => 'nuevo.jugador@example.com'
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Jugador creado exitosamente', $client->getResponse()->getContent());
    }

    public function testCreateJugadorMissingData()
    {
        $client = static::createClient();
        $client->request('POST', '/api/create-jugador', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            
        ]));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Faltan datos obligatorios', $client->getResponse()->getContent());
    }

  
}