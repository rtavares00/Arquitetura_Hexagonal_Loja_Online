<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class EstoqueInsuficiente extends DomainException
{
    public function __construct()
    {
        parent::__construct('Estoque insuficiente para dar baixa na quantidade informada.');
    }
}
