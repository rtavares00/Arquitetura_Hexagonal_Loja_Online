<?php

namespace Tavares\LojaOnline\UseCase;

use Tavares\LojaOnline\Domain\Pedido;
use Tavares\LojaOnline\Domain\VO\Email;
use Tavares\LojaOnline\Domain\Exception\CarrinhoVazioNaoPossuiTotal;
use Tavares\LojaOnline\Port\CarrinhoRepository;
use Tavares\LojaOnline\Port\ProdutoRepository;
use Tavares\LojaOnline\Port\PedidoRepository;
use Tavares\LojaOnline\Port\NotificadorPedido;

/*
busca o carrinho, e então:

valida que o carrinho não está vazio;
pra cada item, valida estoque e dá baixa no produto;
calcula o total (somando os Dinheiro de cada item);
cria o Pedido;
salva produtos (com estoque atualizado) e o pedido;
notifica o cliente.
 */
class FinalizarPedido{
    
    public function __construct(
        private CarrinhoRepository $carrinhoRepository,
        private ProdutoRepository $produtoRepository,
        private PedidoRepository $pedidoRepository,
        private NotificadorPedido $notificador
    )
    {

    }

    public function executar(int $carrinhoID, Email $cliente):void
    {
        // 1. busca o carrinho
        $carrinho = $this->carrinhoRepository->buscar($carrinhoID);

        // 2. valida que o carrinho não está vazio
        if($carrinho->estaVazio()):
            throw new CarrinhoVazioNaoPossuiTotal();
        endif;

        // 3. pra cada item, dá baixa no estoque (valida internamente) e salva o produto
        foreach($carrinho->itens() as $item):
            $produto = $item->produto();
            $produto->darBaixa($item->quantidade());
            $this->produtoRepository->salvar($produto);
        endforeach;

        // 4. calcula o total
        $total = $carrinho->calcularTotal();

        // 5. cria o Pedido (repositório gera a identidade)
        $pedido = new Pedido(
            $this->pedidoRepository->proximoId(),
            $carrinho->itens(),
            $total,
            $cliente
        );

        // 6. salva o pedido
        $this->pedidoRepository->salvar($pedido);

        // 7. notifica o cliente
        $this->notificador->avisar($cliente);
    }
    
}