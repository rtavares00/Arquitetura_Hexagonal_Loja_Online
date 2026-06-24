<?php

namespace Tavares\LojaOnline\UseCase;

use Tavares\LojaOnline\Domain\VO\Quantidade;
use Tavares\LojaOnline\Domain\VO\ItemCarrinho;
use Tavares\LojaOnline\Port\CarrinhoRepository;
use Tavares\LojaOnline\Port\ProdutoRepository;

//busca o produto, cria/atualiza o carrinho, adiciona o item.
class AdicionarItemCarrinho{

    public function __construct(
        private CarrinhoRepository $carrinhoRepository,
        private ProdutoRepository $produtoRepository
    )
    {
        
    }


    public function executar(int $carrinhoID, int $sku /* CÓDIGO DO PRODUTO */, Quantidade $quantidade):void
    {
        $produto = $this->produtoRepository->buscar($sku);

        $carrinho = $this->carrinhoRepository->buscar($carrinhoID);

        $carrinho->adicionarItem( new ItemCarrinho($produto,$quantidade) );

        $this->carrinhoRepository->salvar($carrinho);
    }
    
}