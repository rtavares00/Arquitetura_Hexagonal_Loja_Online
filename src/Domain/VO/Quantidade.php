<?php

namespace Tavares\LojaOnline\Domain\VO;

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

    public function subtrair(Quantidade $outra):self
    {
        if ($outra->quantidade <= 0):
            throw new QuantidadeParaBaixaDeveSerSuperiorAZero();
        endif;

        if ($outra->quantidade > $this->quantidade):
            throw new QuantidadeNaoPodeSerNegativa();
        endif;

        return new self($this->quantidade - $outra->quantidade);
    }

    public function ehMenorQue(Quantidade $outra):bool
    {
        return $this->quantidade < $outra->quantidade;
    }

    public function obter():int
    {
        return $this->quantidade;
    }
}
