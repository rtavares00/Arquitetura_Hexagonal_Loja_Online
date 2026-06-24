<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class ProdutoNaoEstaNoCarrinho extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('O produto não está no carrinho.');
    }
}
