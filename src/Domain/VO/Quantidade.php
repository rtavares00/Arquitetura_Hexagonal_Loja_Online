<?php

namespace Tavares\LojaOnline\Domain\VO;

use Tavares\LojaOnline\Domain\Exception\EstoqueInsuficiente;
use Tavares\LojaOnline\Domain\Exception\QuantidadeNaoPodeSerNegativa;
use Tavares\LojaOnline\Domain\Exception\QuantidadeParaBaixaDeveSerSuperiorAZero;

final class Quantidade
{
    public function __construct(private int $quantidade)
    {
        if ($quantidade < 0):
            throw new QuantidadeNaoPodeSerNegativa();
        endif;
    }

    public function darBaixa(Quantidade $item):self
    {
        if ($item->quantidade <= 0):
            throw new QuantidadeParaBaixaDeveSerSuperiorAZero();
        endif;

        if ($item->quantidade > $this->quantidade):
            throw new EstoqueInsuficiente();
        endif;

        return new self($this->quantidade - $item->quantidade);
    }

    public function obter():int
    {
        return $this->quantidade;
    }
}