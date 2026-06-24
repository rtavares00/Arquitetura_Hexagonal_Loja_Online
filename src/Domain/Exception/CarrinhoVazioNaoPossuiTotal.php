<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class CarrinhoVazioNaoPossuiTotal extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Não é possível calcular o total de um carrinho vazio.');
    }
}
