<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class ValorMonetarioNaoPodeSerNegativo extends DomainException
{
    public function __construct()
    {
        parent::__construct('O valor do dinheiro não pode ser inferior a 0.');
    }
}
