<?php

namespace Tavares\LojaOnline\UseCase;


use Tavares\LojaOnline\Domain\Carrinho;
use Tavares\LojaOnline\Port\CarrinhoRepository;


//gera a identidade, cria um carrinho vazio, persiste e devolve o ID.
class CriarCarrinho{

    public function __construct(
        private CarrinhoRepository $carrinhoRepository

    )
    {

    }


    public function executar():int
    {
        $id = $this->carrinhoRepository->proximoId();

        $carrinho = new Carrinho($id, []);

        $this->carrinhoRepository->salvar($carrinho);

        return $id;
    }

}