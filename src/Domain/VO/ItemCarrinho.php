<?php

namespace Tavares\LojaOnline\Domain\VO;

use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Domain\VO\Quantidade;

final class ItemCarrinho
{
    public function __construct(private Produto $produto , private Quantidade $quantidadeAdquirir)
    {
        
    }

    public function quantidade():Quantidade
    {
        return $this->quantidadeAdquirir;
    }

    public function produto():Produto
    {
        return $this->produto;
    }
}
