<?php

namespace Tavares\LojaOnline\Domain\VO;

use Tavares\LojaOnline\Domain\Exception\ValorMonetarioNaoPodeSerNegativo;
use Tavares\LojaOnline\Domain\Exception\ValorMonetarioNaoPodeSerZero;

final class MoneyInCents
{
    
    public function __construct(private int $cents)
    {
        if ($this->cents == 0):
            throw new ValorMonetarioNaoPodeSerZero();
        endif;

        if ($this->cents < 0):
            throw new ValorMonetarioNaoPodeSerNegativo();
        endif;
    }

    public function equals(MoneyInCents $other):bool
    {
        return ($this->cents === $other->cents);
    }

    public function isGreatherThan(MoneyInCents $other):bool
    {
        return ($this->cents > $other->cents);
    }

    public function get():int
    {
        return $this->cents;
    }
}
