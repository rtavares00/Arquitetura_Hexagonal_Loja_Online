<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter;

use Tavares\LojaOnline\Domain\VO\Email;
use Tavares\LojaOnline\Port\NotificadorPedido;

final class NotificadorPedidoEmMemoria implements NotificadorPedido
{
    /** @var Email[] clientes que foram avisados (útil para asserts em teste) */
    private array $avisados = [];

    public function avisar(Email $cliente) // notificar o cliente
    {
        $this->avisados[] = $cliente;
    }

    /** @return Email[] */
    public function avisados():array
    {
        return $this->avisados;
    }
}
