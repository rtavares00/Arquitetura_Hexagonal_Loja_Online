<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Domain\Enum;

enum StatusPedido: string
{
    case Pendente = 'pendente';
    case Pago = 'pago';
    case Enviado = 'enviado';
    case Entregue = 'entregue';
    case Cancelado = 'cancelado';
}
