<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class CarrinhoNaoEncontrado extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Carrinho não encontrado.');
    }
}
