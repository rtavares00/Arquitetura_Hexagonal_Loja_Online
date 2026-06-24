<?php

namespace Tavares\LojaOnline\Domain\Exception;

final class EmailInvalido extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('O e-mail informado não é válido.');
    }
}
