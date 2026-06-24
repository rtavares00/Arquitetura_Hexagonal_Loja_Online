<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class ProdutoJaEstaNoCarrinho extends DomainException
{
    public function __construct()
    {
        parent::__construct('O produto já está no carrinho.');
    }
}
