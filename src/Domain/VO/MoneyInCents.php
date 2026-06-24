<?php

namespace Tavares\LojaOnline\Domain\VO;

use Tavares\LojaOnline\Domain\Exception\ValorMonetarioNaoPodeSerNegativo;

final class MoneyInCents
{

    public function __construct(private int $cents)
    {
        if ($this->cents < 0):
            throw new ValorMonetarioNaoPodeSerNegativo();
        endif;
    }

    public function somar(MoneyInCents $other):MoneyInCents
    {
        return new MoneyInCents($this->cents + $other->cents);
    }

    public function multiplicarPor(int $fator):MoneyInCents
    {
        return new MoneyInCents($this->cents * $fator);
    }

    public function equals(MoneyInCents $other):bool
    {
        return ($this->cents === $other->cents);
    }

    public function isGreaterThan(MoneyInCents $other):bool
    {
        return ($this->cents > $other->cents);
    }

    public function get():int
    {
        return $this->cents;
    }
}
