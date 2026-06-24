<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter;

use Tavares\LojaOnline\Domain\Pedido;
use Tavares\LojaOnline\Port\PedidoRepository;

final class PedidoRepositoryEmMemoria implements PedidoRepository
{
    /** @var array<int,Pedido> indexado pelo ID */
    private array $pedidos = [];

    private int $sequencia = 1;

    public function proximoId():int
    {
        return $this->sequencia++;
    }

    public function salvar(Pedido $pedido):void
    {
        $this->pedidos[$pedido->getId()] = $pedido;
    }
}
