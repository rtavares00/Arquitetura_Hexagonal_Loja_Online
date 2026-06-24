<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Port;

use Tavares\LojaOnline\Domain\Carrinho;

interface CarrinhoRepository
{
    public function proximoId():int;
    public function buscar(int $id):Carrinho;
    public function salvar(Carrinho $carrinho):void;
}
