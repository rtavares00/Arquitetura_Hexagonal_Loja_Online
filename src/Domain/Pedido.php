<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Domain;

use Tavares\LojaOnline\Domain\Enum\StatusPedido;
use Tavares\LojaOnline\Domain\VO\Email;
use Tavares\LojaOnline\Domain\VO\ItemCarrinho;
use Tavares\LojaOnline\Domain\VO\MoneyInCents;
//resultado do checkout: id, itens, Dinheiro total, status.

class Pedido{
    
    private StatusPedido $status;    

    /** @param ItemCarrinho[] $itens */
    public function __construct
    (
        private int $id ,
        private array $itens,
        private MoneyInCents $amount,
        private Email $cliente
        //private string $status
    )
    {
        $this->status = StatusPedido::Pendente;
    }

    public function getId():int
    {
        return $this->id;
    }

    /** @return ItemCarrinho[] */
    public function getItens():array
    {
        return $this->itens;
    }

    public function getTotal():MoneyInCents
    {
        return $this->amount;
    }

    public function getCliente():Email
    {
        return $this->cliente;
    }

    public function getStatus():StatusPedido
    {
        return $this->status;
    }

}