<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Port;

use Tavares\LojaOnline\Domain\Pedido;

interface PedidoRepository
{
    public function proximoId():int;
    public function salvar(Pedido $pedido):void;
}
