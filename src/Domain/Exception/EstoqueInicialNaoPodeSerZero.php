<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class EstoqueInicialNaoPodeSerZero extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('O estoque do produto não pode ser zerado.');
    }
}
