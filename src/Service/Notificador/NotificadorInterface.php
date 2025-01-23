<?php

namespace App\Service\Notificador;

/**
 * Interfaz NotificadorInterface
 * ( interfaz de Estrategia en el patrón Strategy )
 * ( interfaz de Observador en el patrón Observer )
 *
 * Esta interfaz define el contrato para las estrategias de notificación en el patrón Strategy.
 * Las implementaciones concretas de esta interfaz (o estrategias) implementarán el método 'notificar',
 * que se encargará de enviar notificaciones a los destinatarios especificados.
 * 
 * De esta forma dejamos abierta la posibilidad de poder implementar otras formas de notificación
 * tales como SMS o Whatsapp, sin tener que implementarlas actualmente.
 * 
 * Para esta prueba implementaremos la estrategia concreta NotificadorEmail
 * En un futuro podremos implementar otras estrategias como NotificadorSMS o NotificadorWhatsapp
 *  
 */
interface NotificadorInterface
{
    public function notificar(string $mensaje, object $destinatario): void;
}
