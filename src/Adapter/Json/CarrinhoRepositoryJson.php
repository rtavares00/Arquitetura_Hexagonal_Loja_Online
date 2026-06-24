<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\Json;

use Tavares\LojaOnline\Domain\Carrinho;
use Tavares\LojaOnline\Domain\VO\ItemCarrinho;
use Tavares\LojaOnline\Domain\VO\Quantidade;
use Tavares\LojaOnline\Domain\Exception\CarrinhoNaoEncontrado;
use Tavares\LojaOnline\Port\CarrinhoRepository;

final class CarrinhoRepositoryJson implements CarrinhoRepository
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

    public function buscar(int $id):Carrinho
    {
        $dados = $this->estrutura();

        if(!isset($dados['registros'][$id])):
            throw new CarrinhoNaoEncontrado();
        endif;

        return $this->deArray($dados['registros'][$id]);
    }

    public function salvar(Carrinho $carrinho):void
    {
        $dados = $this->estrutura();
        $dados['registros'][$carrinho->getId()] = $this->paraArray($carrinho);
        $this->gravar($this->arquivo, $dados);
    }

    private function estrutura():array
    {
        return $this->ler($this->arquivo) + ['sequencia' => 1, 'registros' => []];
    }

    private function paraArray(Carrinho $carrinho):array
    {
        $itens = [];

        foreach($carrinho->itens() as $item):
            $itens[] = [
                'produto'    => ProdutoJsonMap::paraArray($item->produto()),
                'quantidade' => $item->quantidade()->obter(),
            ];
        endforeach;

        return ['id' => $carrinho->getId(), 'itens' => $itens];
    }

    private function deArray(array $dados):Carrinho
    {
        $itens = [];

        foreach($dados['itens'] as $item):
            $itens[] = new ItemCarrinho(
                ProdutoJsonMap::deArray($item['produto']),
                new Quantidade($item['quantidade'])
            );
        endforeach;

        return new Carrinho($dados['id'], $itens);
    }
}
