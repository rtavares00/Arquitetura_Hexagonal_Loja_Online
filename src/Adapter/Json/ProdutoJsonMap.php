<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\Json;

use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Domain\VO\MoneyInCents;
use Tavares\LojaOnline\Domain\VO\Quantidade;

// converte Produto <-> array (reusado pelos adapters de Produto, Carrinho e Pedido)
final class ProdutoJsonMap
{
    public static function paraArray(Produto $produto):array
    {
        return [
            'sku'        => $produto->getSKU(),
            'nome'       => $produto->getNome(),
            'precoCents' => $produto->getPreco()->get(),
            'estoque'    => $produto->getQuantidadeEmEstoque()->obter(),
        ];
    }

    public static function deArray(array $dados):Produto
    {
        return new Produto(
            $dados['sku'],
            $dados['nome'],
            new MoneyInCents($dados['precoCents']),
            new Quantidade($dados['estoque'])
        );
    }
}
