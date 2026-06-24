<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter;

use Tavares\LojaOnline\Domain\Carrinho;
use Tavares\LojaOnline\Domain\Exception\CarrinhoNaoEncontrado;
use Tavares\LojaOnline\Port\CarrinhoRepository;

final class CarrinhoRepositoryEmMemoria implements CarrinhoRepository
{
    /** @var array<int,Carrinho> indexado pelo ID */
    private array $carrinhos = [];

    private int $sequencia = 1;

    public function proximoId():int
    {
        return $this->sequencia++;
    }

    public function buscar(int $id):Carrinho
    {
        if(!isset($this->carrinhos[$id])):
            throw new CarrinhoNaoEncontrado();
        endif;

        return $this->carrinhos[$id];
    }

    public function salvar(Carrinho $carrinho):void
    {
        $this->carrinhos[$carrinho->getId()] = $carrinho;
    }
}
