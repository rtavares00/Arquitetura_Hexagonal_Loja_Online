<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Domain;
use Tavares\LojaOnline\Domain\VO\MoneyInCents;
use Tavares\LojaOnline\Domain\VO\Quantidade;
use Tavares\LojaOnline\Domain\Exception\EstoqueInicialNaoPodeSerZero;

class Produto
{
    public function __construct(private int $sku,private string $nome,private MoneyInCents $preco,private Quantidade $quantidadeNoEstoque)
    {
        if ($quantidadeNoEstoque->obter() <= 0):
            throw new EstoqueInicialNaoPodeSerZero();
        endif;
    }

    public function getNome():string
    {
        return $this->nome;
    }

    public function getPreco():MoneyInCents
    {
        return $this->preco;
    }

    public function getSKU():int
    {
        return $this->sku;
    }

    public function getQuantidadeEmEstoque():Quantidade
    {
        return $this->quantidadeNoEstoque;
    }

    public function darBaixa(Quantidade $quantidade):void
    {        
        $this->quantidadeNoEstoque = $this->quantidadeNoEstoque->darBaixa($quantidade);
    }
}
