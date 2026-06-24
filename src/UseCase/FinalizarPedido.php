<?php

namespace Tavares\LojaOnline\UseCase;

use Tavares\LojaOnline\Domain\Pedido;
use Tavares\LojaOnline\Domain\VO\Email;
use Tavares\LojaOnline\Domain\Exception\CarrinhoVazioNaoPossuiTotal;
use Tavares\LojaOnline\Domain\Exception\EstoqueInsuficiente;
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

        // 3. FASE 1 — checar TODOS os itens (não muta, não persiste).
        //    Aborta antes de mexer em qualquer estoque se faltar um só.
        foreach($carrinho->itens() as $item):
            if(!$item->produto()->temEstoquePara($item->quantidade())):
                throw new EstoqueInsuficiente();
            endif;
        endforeach;

        // 4. FASE 2 — aplicar a baixa em todos (agora é seguro) e persistir
        foreach($carrinho->itens() as $item):
            $produto = $item->produto();
            $produto->darBaixa($item->quantidade());
            $this->produtoRepository->salvar($produto);
        endforeach;

        // 5. calcula o total
        $total = $carrinho->calcularTotal();

        // 6. cria o Pedido (repositório gera a identidade)
        $pedido = new Pedido(
            $this->pedidoRepository->proximoId(),
            $carrinho->itens(),
            $total,
            $cliente
        );

        // 7. salva o pedido e notifica o cliente
        $this->pedidoRepository->salvar($pedido);
        $this->notificador->avisar($cliente);
    }
    
}