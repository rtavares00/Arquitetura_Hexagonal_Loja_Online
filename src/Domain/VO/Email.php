<?php

namespace Tavares\LojaOnline\Domain\VO;

use Tavares\LojaOnline\Domain\Exception\EmailInvalido;

final class Email
{
    public function __construct(private string $email)
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false):
            throw new EmailInvalido();
        endif;
    }

    public function get():string
    {
        return $this->email;
    }
}
