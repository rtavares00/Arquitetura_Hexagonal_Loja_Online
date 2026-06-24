<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class QuantidadeNaoPodeSerNegativa extends DomainException
{
    public function __construct()
    {
        parent::__construct('A quantidade não pode ser inferior a 0.');
    }
}
