<?php

namespace Tavares\LojaOnline\Domain\Exception;

// Base de todas as violações de regra de negócio.
// Permite capturar qualquer erro de domínio com um único catch
// e separá-los dos erros de infraestrutura.
abstract class DomainException extends \RuntimeException
{
}
