<?php

declare(strict_types=1);

namespace Tavares\LojaOnline;

use Tavares\LojaOnline\Adapter\CarrinhoRepositoryEmMemoria;
use Tavares\LojaOnline\Adapter\ProdutoRepositoryEmMemoria;
use Tavares\LojaOnline\Adapter\PedidoRepositoryEmMemoria;
use Tavares\LojaOnline\Adapter\NotificadorPedidoEmMemoria;
use Tavares\LojaOnline\Adapter\Json\CarrinhoRepositoryJson;
use Tavares\LojaOnline\Adapter\Json\ProdutoRepositoryJson;
use Tavares\LojaOnline\Adapter\Json\PedidoRepositoryJson;
use Tavares\LojaOnline\Adapter\Json\NotificadorPedidoArquivo;
use Tavares\LojaOnline\Adapter\MySQL\CarrinhoRepositoryMySQL;
use Tavares\LojaOnline\Adapter\MySQL\ProdutoRepositoryMySQL;
use Tavares\LojaOnline\Adapter\MySQL\PedidoRepositoryMySQL;
use Tavares\LojaOnline\Port\CarrinhoRepository;
use Tavares\LojaOnline\Port\ProdutoRepository;
use Tavares\LojaOnline\Port\PedidoRepository;
use Tavares\LojaOnline\Port\NotificadorPedido;
use Tavares\LojaOnline\UseCase\CriarCarrinho;
use Tavares\LojaOnline\UseCase\AdicionarItemCarrinho;
use Tavares\LojaOnline\UseCase\FinalizarPedido;

// Composition root: o ÚNICO lugar que decide quais adapters concretos usar.
// Cada fábrica estática (comMemoria/comJson/comMySQL) monta um conjunto de adapters.
final class Container
{
    public function __construct(
        private CarrinhoRepository $carrinhoRepository,
        private ProdutoRepository $produtoRepository,
        private PedidoRepository $pedidoRepository,
        private NotificadorPedido $notificador
    )
    {
    }

    public static function comMemoria():self
    {
        return new self(
            new CarrinhoRepositoryEmMemoria(),
            new ProdutoRepositoryEmMemoria(),
            new PedidoRepositoryEmMemoria(),
            new NotificadorPedidoEmMemoria()
        );
    }

    public static function comJson(string $diretorioDados):self
    {
        return new self(
            new CarrinhoRepositoryJson($diretorioDados . '/carrinhos.json'),
            new ProdutoRepositoryJson($diretorioDados . '/produtos.json'),
            new PedidoRepositoryJson($diretorioDados . '/pedidos.json'),
            new NotificadorPedidoArquivo($diretorioDados . '/notificacoes.json')
        );
    }

    public static function comMySQL(\PDO $pdo):self
    {
        return new self(
            new CarrinhoRepositoryMySQL($pdo),
            new ProdutoRepositoryMySQL($pdo),
            new PedidoRepositoryMySQL($pdo),
            // notificação não é persistência: reaproveita o adapter em memória.
            new NotificadorPedidoEmMemoria()
        );
    }

    public function criarCarrinho():CriarCarrinho
    {
        return new CriarCarrinho($this->carrinhoRepository);
    }

    public function adicionarItemCarrinho():AdicionarItemCarrinho
    {
        return new AdicionarItemCarrinho($this->carrinhoRepository, $this->produtoRepository);
    }

    public function finalizarPedido():FinalizarPedido
    {
        return new FinalizarPedido(
            $this->carrinhoRepository,
            $this->produtoRepository,
            $this->pedidoRepository,
            $this->notificador
        );
    }

    // exposto para semear produtos (cadastro inicial de estoque)
    public function produtoRepository():ProdutoRepository
    {
        return $this->produtoRepository;
    }
}
