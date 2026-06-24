<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Domain;
use Tavares\LojaOnline\Domain\VO\MoneyInCents;
use Tavares\LojaOnline\Domain\VO\Quantidade;
use Tavares\LojaOnline\Domain\Exception\EstoqueInsuficiente;

class Produto
{
    public function __construct(private int $sku,private string $nome,private MoneyInCents $preco,private Quantidade $quantidadeNoEstoque)
    {
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

    // consulta pura: não muta nada (usado na fase de validação do checkout)
    public function temEstoquePara(Quantidade $quantidade):bool
    {
        return !$this->quantidadeNoEstoque->ehMenorQue($quantidade);
    }

    public function darBaixa(Quantidade $quantidade):void
    {
        if (!$this->temEstoquePara($quantidade)):
            throw new EstoqueInsuficiente();
        endif;

        $this->quantidadeNoEstoque = $this->quantidadeNoEstoque->subtrair($quantidade);
    }
}
