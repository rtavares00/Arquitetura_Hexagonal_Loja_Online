<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class QuantidadeParaBaixaDeveSerSuperiorAZero extends DomainException
{
    public function __construct()
    {
        parent::__construct('A quantidade a ser dada baixa tem que ser superior a 0.');
    }
}
