<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\MySQL;

use Tavares\LojaOnline\Domain\Carrinho;
use Tavares\LojaOnline\Port\CarrinhoRepository;

// STUB: assinaturas prontas, persistência ainda não implementada.
final class CarrinhoRepositoryMySQL implements CarrinhoRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function proximoId():int
    {
        throw new \LogicException('CarrinhoRepositoryMySQL::proximoId ainda não implementado.');
    }

    public function buscar(int $id):Carrinho
    {
        throw new \LogicException('CarrinhoRepositoryMySQL::buscar ainda não implementado.');
    }

    public function salvar(Carrinho $carrinho):void
    {
        throw new \LogicException('CarrinhoRepositoryMySQL::salvar ainda não implementado.');
    }
}
