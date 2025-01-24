<?php 
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EntrenadorControllerTest extends WebTestCase
{
    public function testBajaEntrenador()
    {
        $client = static::createClient();
        $client->request('POST', '/api/entrenador/1/delete');

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Entrenador dado de baja del club', $client->getResponse()->getContent());
    }

    public function testBajaEntrenadorNoEncontrado()
    {
        $client = static::createClient();
        $client->request('POST', '/api/entrenador/999/delete');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Entrenador no encontrado', $client->getResponse()->getContent());
    }

    public function testBajaEntrenadorSinClub()
    {
        $client = static::createClient();
        $client->request('POST', '/api/entrenador/2/delete');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('El Entrenador no pertenece a ningún Club', $client->getResponse()->getContent());
    }

    public function testBajaEntrenadorNotificationError()
    {
        $client = static::createClient();
        $client->request('POST', '/api/entrenador/3/delete');

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('Entrenador dado de baja del club', $client->getResponse()->getContent());
        $this->assertStringContainsString('pero hubo un problema al enviar la notificación', $client->getResponse()->getContent());
    }
}
