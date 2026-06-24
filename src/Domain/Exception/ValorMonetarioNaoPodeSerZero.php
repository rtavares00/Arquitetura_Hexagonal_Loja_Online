<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class ValorMonetarioNaoPodeSerZero extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('O valor do dinheiro não pode ser 0.');
    }
}
