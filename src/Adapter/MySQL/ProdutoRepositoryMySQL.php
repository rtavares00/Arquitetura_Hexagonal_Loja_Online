<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\MySQL;

use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Port\ProdutoRepository;

// STUB: assinaturas prontas, persistência ainda não implementada.
final class ProdutoRepositoryMySQL implements ProdutoRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function buscar(int $sku):Produto
    {
        throw new \LogicException('ProdutoRepositoryMySQL::buscar ainda não implementado.');
    }

    public function salvar(Produto $produto):void
    {
        throw new \LogicException('ProdutoRepositoryMySQL::salvar ainda não implementado.');
    }
}
