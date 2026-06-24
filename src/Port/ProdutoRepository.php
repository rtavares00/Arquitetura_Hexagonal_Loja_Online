<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Port;

use Tavares\LojaOnline\Domain\Produto;

interface ProdutoRepository
{
    public function buscar(int $sku):Produto;
    public function salvar(Produto $produto):void;
}
