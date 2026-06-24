<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Port;

use Tavares\LojaOnline\Domain\VO\Email;

interface NotificadorPedido
{
    public function avisar(Email $cliente):void; // notificar o cliente
}
