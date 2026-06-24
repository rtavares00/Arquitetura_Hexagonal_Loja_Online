<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\MySQL;

use Tavares\LojaOnline\Domain\Pedido;
use Tavares\LojaOnline\Port\PedidoRepository;

// STUB: assinaturas prontas, persistência ainda não implementada.
final class PedidoRepositoryMySQL implements PedidoRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function proximoId():int
    {
        throw new \LogicException('PedidoRepositoryMySQL::proximoId ainda não implementado.');
    }

    public function salvar(Pedido $pedido):void
    {
        throw new \LogicException('PedidoRepositoryMySQL::salvar ainda não implementado.');
    }
}
