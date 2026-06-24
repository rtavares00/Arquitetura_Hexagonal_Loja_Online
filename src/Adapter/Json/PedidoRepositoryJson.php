<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\Json;

use Tavares\LojaOnline\Domain\Pedido;
use Tavares\LojaOnline\Port\PedidoRepository;

final class PedidoRepositoryJson implements PedidoRepository
{
    use LeituraEscritaJson;

    public function __construct(private string $arquivo)
    {
    }

    public function proximoId():int
    {
        $dados = $this->estrutura();
        $id = $dados['sequencia'];
        $dados['sequencia'] = $id + 1;
        $this->gravar($this->arquivo, $dados);

        return $id;
    }

    public function salvar(Pedido $pedido):void
    {
        $dados = $this->estrutura();
        $dados['registros'][$pedido->getId()] = $this->paraArray($pedido);
        $this->gravar($this->arquivo, $dados);
    }

    private function estrutura():array
    {
        return $this->ler($this->arquivo) + ['sequencia' => 1, 'registros' => []];
    }

    private function paraArray(Pedido $pedido):array
    {
        $itens = [];

        foreach($pedido->getItens() as $item):
            $itens[] = [
                'produto'    => ProdutoJsonMap::paraArray($item->produto()),
                'quantidade' => $item->quantidade()->obter(),
            ];
        endforeach;

        return [
            'id'         => $pedido->getId(),
            'itens'      => $itens,
            'totalCents' => $pedido->getTotal()->get(),
            'cliente'    => $pedido->getCliente()->get(),
            'status'     => $pedido->getStatus()->value,
        ];
    }
}
