<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter;

use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Domain\Exception\ProdutoNaoEncontrado;
use Tavares\LojaOnline\Port\ProdutoRepository;

final class ProdutoRepositoryEmMemoria implements ProdutoRepository
{
    /** @var array<int,Produto> indexado pelo SKU */
    private array $produtos = [];

    public function buscar(int $sku):Produto
    {
        if(!isset($this->produtos[$sku])):
            throw new ProdutoNaoEncontrado();
        endif;

        return $this->produtos[$sku];
    }

    public function salvar(Produto $produto):void
    {
        $this->produtos[$produto->getSKU()] = $produto;
    }
}
