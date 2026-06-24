<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Domain;
use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Domain\VO\MoneyInCents;
use Tavares\LojaOnline\Domain\VO\Quantidade;
use Tavares\LojaOnline\Domain\VO\ItemCarrinho;
use Tavares\LojaOnline\Domain\Exception\ProdutoJaEstaNoCarrinho;
use Tavares\LojaOnline\Domain\Exception\ProdutoNaoEstaNoCarrinho;
use Tavares\LojaOnline\Domain\Exception\CarrinhoVazioNaoPossuiTotal;

class Carrinho
{
    public function __construct(private int $id ,private array $itens)
    {

    }

    public function getId():int
    {
        return $this->id;
    }

    /** @return ItemCarrinho[] */
    public function itens():array
    {
        return $this->itens;
    }

    public function estaVazio():bool
    {
        return empty($this->itens);
    }

    public function isItemInCarrinho(Produto $produto):bool
    {
        foreach($this->itens as $item):
            if($item->produto()->getSKU() === $produto->getSKU()):
                return true;
            endif;
        endforeach;

        return false;
    }

    public function adicionarItem(ItemCarrinho $item):void
    {
        if($this->isItemInCarrinho($item->produto())):
            throw new ProdutoJaEstaNoCarrinho();
        endif;

        array_push($this->itens, $item);
    }

    public function removerItem(ItemCarrinho $item):void
    {
        if(!$this->isItemInCarrinho($item->produto())):
            throw new ProdutoNaoEstaNoCarrinho();
        endif;

        for($c = 0; $c < count($this->itens); $c++)
        {
            if($this->itens[$c]->produto()->getSKU() === $item->produto()->getSKU()):
                array_splice($this->itens,$c,1);
                break;
            endif;
        }
    }

    
    public function calcularTotal():MoneyInCents
    {
        if($this->estaVazio()):
            throw new CarrinhoVazioNaoPossuiTotal();
        endif;

        $total = new MoneyInCents(0);

        foreach($this->itens as $item):
            $total = $total->somar(
                $item->produto()->getPreco()->multiplicarPor($item->quantidade()->obter())
            );
        endforeach;

        return $total;
    }
    
}
