<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Tavares\LojaOnline\Container;
use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Domain\VO\Quantidade;
use Tavares\LojaOnline\Domain\VO\MoneyInCents;
use Tavares\LojaOnline\Domain\VO\Email;

// Adapter padrão: JSON (arquivos em projeto/data/*.json).
$container = Container::comJson(__DIR__ . '/../data');

// --- Seleção de driver por linha de comando (desativada por enquanto) ---
// $driver = $argv[1] ?? 'json';
// $container = match ($driver) {
//     'memoria' => Container::comMemoria(),
//     'json'    => Container::comJson(__DIR__ . '/../data'),
//     'mysql'   => Container::comMySQL(new \PDO(
//         getenv('DB_DSN')  ?: 'mysql:host=localhost;dbname=loja_online;charset=utf8mb4',
//         getenv('DB_USER') ?: 'root',
//         getenv('DB_PASS') ?: ''
//     )),
//     default   => throw new \InvalidArgumentException("Driver desconhecido: \"{$driver}\"."),
// };

// Aciona os casos de uso com os argumentos passados na linha de comando:
//   php src/index.php cadastrar-produto <sku> <nome> <precoCents> <estoque>
//   php src/index.php criar-carrinho
//   php src/index.php adicionar-item <carrinhoID> <sku> <quantidade>
//   php src/index.php finalizar <carrinhoID> <email>
$comando = $argv[1] ?? null;

switch ($comando) {
    case 'cadastrar-produto':
        $container->produtoRepository()->salvar(new Produto(
            (int) $argv[2],
            (string) $argv[3],
            new MoneyInCents((int) $argv[4]),
            new Quantidade((int) $argv[5])
        ));
        echo "Produto {$argv[2]} cadastrado.\n";
        break;

    case 'criar-carrinho':
        $id = $container->criarCarrinho()->executar();
        echo "Carrinho criado: #{$id}\n";
        break;

    case 'adicionar-item':
        $container->adicionarItemCarrinho()->executar(
            (int) $argv[2],
            (int) $argv[3],
            new Quantidade((int) $argv[4])
        );
        echo "Item (sku {$argv[3]}, qtd {$argv[4]}) adicionado ao carrinho #{$argv[2]}.\n";
        break;

    case 'finalizar':
        $container->finalizarPedido()->executar(
            (int) $argv[2],
            new Email((string) $argv[3])
        );
        echo "Pedido do carrinho #{$argv[2]} finalizado.\n";
        break;

    default:
        echo "Comandos disponíveis:\n";
        echo "  cadastrar-produto <sku> <nome> <precoCents> <estoque>\n";
        echo "  criar-carrinho\n";
        echo "  adicionar-item <carrinhoID> <sku> <quantidade>\n";
        echo "  finalizar <carrinhoID> <email>\n";
        break;
}
