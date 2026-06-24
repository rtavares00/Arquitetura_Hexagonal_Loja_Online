<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\Json;

use Tavares\LojaOnline\Domain\VO\Email;
use Tavares\LojaOnline\Port\NotificadorPedido;

final class NotificadorPedidoArquivo implements NotificadorPedido
{
    use LeituraEscritaJson;

    public function __construct(private string $arquivo)
    {
    }

    public function avisar(Email $cliente) // notificar o cliente
    {
        $log = $this->ler($this->arquivo);

        $log[] = [
            'cliente' => $cliente->get(),
            'em'      => date(DATE_ATOM),
        ];

        $this->gravar($this->arquivo, $log);
    }
}
