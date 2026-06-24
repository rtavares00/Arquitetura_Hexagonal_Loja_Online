<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class ProdutoNaoEncontrado extends DomainException
{
    public function __construct()
    {
        parent::__construct('Produto não encontrado.');
    }
}
