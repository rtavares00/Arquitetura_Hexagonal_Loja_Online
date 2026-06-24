# 🛒 Loja Online — Arquitetura Hexagonal (Ports & Adapters)

Aplicação de exemplo, em **PHP 8.1+**, que implementa o núcleo de uma loja online
(cadastro de produtos, carrinho de compras e finalização de pedido) seguindo a
**Arquitetura Hexagonal** (também conhecida como _Ports & Adapters_).

O objetivo do projeto é didático: demonstrar como isolar as **regras de negócio**
de detalhes de infraestrutura (banco de dados, arquivos, e-mail, interface),
de modo que o domínio não dependa de nada externo — quem depende é sempre o
mundo de fora.

---

## 📑 Índice

- [Conceito: por que Hexagonal?](#-conceito-por-que-hexagonal)
- [Estrutura de pastas](#-estrutura-de-pastas)
- [O domínio](#-o-domínio)
- [Ports (interfaces)](#-ports-interfaces)
- [Adapters (implementações)](#-adapters-implementações)
- [Casos de uso](#-casos-de-uso)
- [Composition Root (Container)](#-composition-root-container)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Como usar (CLI)](#-como-usar-cli)
- [Fluxo completo de exemplo](#-fluxo-completo-de-exemplo)
- [Trocando o mecanismo de persistência](#-trocando-o-mecanismo-de-persistência)
- [Regras de negócio garantidas pelo domínio](#-regras-de-negócio-garantidas-pelo-domínio)
- [Decisões de design](#-decisões-de-design)
- [Autor](#-autor)

---

## 🧭 Conceito: por que Hexagonal?

Na arquitetura hexagonal o **domínio fica no centro** e não conhece detalhes de
infraestrutura. A comunicação com o mundo externo acontece sempre através de
**Ports** (interfaces) implementadas por **Adapters** (classes concretas).

```
                         ┌─────────────────────────────────────┐
        Entrada          │                                     │          Saída
   (CLI / HTTP / ...)     │            CASOS DE USO              │   (JSON / MySQL / ...)
                         │   CriarCarrinho · AdicionarItem ·    │
   index.php ──────────► │       FinalizarPedido                │ ──► ProdutoRepository
                         │                                     │ ──► CarrinhoRepository
                         │   ┌─────────────────────────────┐    │ ──► PedidoRepository
                         │   │          DOMÍNIO            │    │ ──► NotificadorPedido
                         │   │  Produto · Carrinho · Pedido │    │
                         │   │  VOs: Money, Quantidade...   │    │
                         │   └─────────────────────────────┘    │
                         │                                     │
                         └─────────────────────────────────────┘
        ▲ adapters de entrada            ▲ Ports = interfaces      ▲ adapters de saída
```

**Regra de ouro:** a dependência aponta sempre para dentro. O domínio define as
interfaces (`Port/`); a infraestrutura (`Adapter/`) as implementa. Trocar JSON por
MySQL não exige tocar em uma linha sequer de regra de negócio.

---

## 📁 Estrutura de pastas

```
LOJA_ONLINE/
├── composer.json
├── src/
│   ├── index.php                  # Adapter de entrada (CLI)
│   ├── Container.php              # Composition Root (monta os adapters)
│   │
│   ├── Domain/                    # ❤️ Núcleo — sem dependências externas
│   │   ├── Produto.php
│   │   ├── Carrinho.php
│   │   ├── Pedido.php
│   │   ├── Enum/
│   │   │   └── StatusPedido.php
│   │   ├── VO/                    # Value Objects
│   │   │   ├── Email.php
│   │   │   ├── MoneyInCents.php
│   │   │   ├── Quantidade.php
│   │   │   └── ItemCarrinho.php
│   │   └── Exception/             # Exceções de domínio
│   │
│   ├── Port/                      # 🔌 Interfaces (contratos)
│   │   ├── ProdutoRepository.php
│   │   ├── CarrinhoRepository.php
│   │   ├── PedidoRepository.php
│   │   └── NotificadorPedido.php
│   │
│   ├── UseCase/                   # 🎬 Orquestração das regras
│   │   ├── CriarCarrinho.php
│   │   ├── AdicionarItemCarrinho.php
│   │   └── FinalizarPedido.php
│   │
│   └── Adapter/                   # ⚙️ Implementações dos Ports
│       ├── *EmMemoria.php         # voláteis (testes / experimentação)
│       ├── Json/                  # persistência em arquivos .json
│       └── MySQL/                 # persistência em banco relacional
│
└── data/                          # arquivos .json gerados em runtime (ignorado no git)
```

---

## ❤️ O domínio

O coração da aplicação. Nenhuma destas classes importa banco, arquivo ou framework.

| Tipo | Arquivo | Responsabilidade |
|------|---------|------------------|
| Entidade | [`Produto`](src/Domain/Produto.php) | SKU, nome, preço e estoque. Sabe `darBaixa()` validando estoque. |
| Entidade | [`Carrinho`](src/Domain/Carrinho.php) | Coleção de itens; impede item duplicado; calcula o total. |
| Entidade | [`Pedido`](src/Domain/Pedido.php) | Resultado do checkout: itens, total, cliente e status. |
| Enum | [`StatusPedido`](src/Domain/Enum/StatusPedido.php) | `Pendente`, `Pago`, `Enviado`, `Entregue`, `Cancelado`. |

### Value Objects

Pequenos tipos imutáveis que **validam a si mesmos** na construção — um valor
inválido nunca chega a existir:

| VO | Garante |
|----|---------|
| [`MoneyInCents`](src/Domain/VO/MoneyInCents.php) | Dinheiro em centavos (inteiro). Nunca negativo. Operações `somar` / `multiplicarPor`. |
| [`Quantidade`](src/Domain/VO/Quantidade.php) | Inteiro ≥ 0. `subtrair` não permite resultado negativo. |
| [`Email`](src/Domain/VO/Email.php) | Endereço válido (`FILTER_VALIDATE_EMAIL`). |
| [`ItemCarrinho`](src/Domain/VO/ItemCarrinho.php) | Par produto + quantidade adquirida. |

> 💡 **Dinheiro em centavos:** evita os erros clássicos de ponto flutuante.
> R$ 19,90 é representado como `1990`.

---

## 🔌 Ports (interfaces)

Contratos que o domínio exige do mundo externo. Vivem em [`src/Port/`](src/Port/):

```php
interface ProdutoRepository  { buscar(int $sku): Produto;  salvar(Produto): void; }
interface CarrinhoRepository { proximoId(): int; buscar(int $id): Carrinho; salvar(Carrinho): void; }
interface PedidoRepository   { proximoId(): int; salvar(Pedido): void; }
interface NotificadorPedido  { avisar(Email $cliente): void; }
```

---

## ⚙️ Adapters (implementações)

Cada Port tem três implementações intercambiáveis:

| Driver | Pasta | Persistência | Uso recomendado |
|--------|-------|--------------|-----------------|
| **Memória** | [`Adapter/`](src/Adapter/) | Volátil (arrays em memória) | Testes e experimentação |
| **JSON** | [`Adapter/Json/`](src/Adapter/Json/) | Arquivos `data/*.json` | Padrão da CLI |
| **MySQL** | [`Adapter/MySQL/`](src/Adapter/MySQL/) | Banco relacional via PDO | Cenário "produção" |

A notificação no driver MySQL reaproveita o adapter em memória — afinal,
notificar não é persistir.

---

## 🎬 Casos de uso

Orquestram o domínio. Não contêm regra de negócio "dura" — delegam às entidades.

### [`CriarCarrinho`](src/UseCase/CriarCarrinho.php)
Gera a identidade, cria um carrinho vazio, persiste e devolve o ID.

### [`AdicionarItemCarrinho`](src/UseCase/AdicionarItemCarrinho.php)
Busca o produto, busca o carrinho, adiciona o item e salva.

### [`FinalizarPedido`](src/UseCase/FinalizarPedido.php)
O checkout — implementado em **duas fases** para garantir consistência:

1. Busca o carrinho e valida que **não está vazio**.
2. **Fase 1 (validação):** percorre todos os itens checando estoque, sem mutar nada.
   Se faltar **um único** item, aborta antes de mexer em qualquer estoque.
3. **Fase 2 (efetivação):** dá baixa no estoque de todos os itens e persiste.
4. Calcula o total, cria o `Pedido` (com `Pendente`), salva e notifica o cliente.

> 🔒 A separação em duas fases evita um checkout parcial — ou tudo passa, ou nada muda.

---

## 🧩 Composition Root (Container)

[`Container.php`](src/Container.php) é o **único** ponto que decide quais adapters
concretos serão usados. Três fábricas estáticas montam conjuntos completos:

```php
Container::comMemoria();              // tudo em memória
Container::comJson('/caminho/data');  // arquivos .json
Container::comMySQL($pdo);            // banco relacional
```

E expõe os casos de uso já com as dependências injetadas:
`criarCarrinho()`, `adicionarItemCarrinho()`, `finalizarPedido()`.

---

## ✅ Requisitos

- **PHP 8.1 ou superior** (usa `enum`, _constructor property promotion_, tipos estritos).
- **Composer** (apenas para o autoload PSR-4 — não há dependências de terceiros).
- **PDO MySQL** somente se for usar o driver MySQL.

---

## 📦 Instalação

```bash
git clone <url-do-repositorio>
cd LOJA_ONLINE
composer install      # gera o autoloader em vendor/
```

> A pasta `data/` (arquivos JSON em runtime), `vendor/` e `memory/` são ignoradas pelo git.

---

## 🖥️ Como usar (CLI)

O ponto de entrada é [`src/index.php`](src/index.php). Por padrão usa o driver **JSON**,
gravando em `data/*.json`.

```bash
# cadastrar um produto no estoque
php src/index.php cadastrar-produto <sku> <nome> <precoCents> <estoque>

# criar um carrinho vazio (imprime o ID gerado)
php src/index.php criar-carrinho

# adicionar item ao carrinho
php src/index.php adicionar-item <carrinhoID> <sku> <quantidade>

# finalizar o pedido (checkout)
php src/index.php finalizar <carrinhoID> <email>
```

Executar sem argumentos lista os comandos disponíveis.

---

## 🔁 Fluxo completo de exemplo

```bash
# 1. cadastra um produto: SKU 100, "Caneca", R$ 29,90, 50 em estoque
php src/index.php cadastrar-produto 100 "Caneca" 2990 50

# 2. cria um carrinho  ->  Carrinho criado: #1
php src/index.php criar-carrinho

# 3. adiciona 2 unidades do produto 100 ao carrinho #1
php src/index.php adicionar-item 1 100 2

# 4. finaliza o pedido informando o e-mail do cliente
php src/index.php finalizar 1 cliente@exemplo.com
```

Após o checkout: o estoque do produto cai para 48, um `Pedido` é gravado e a
notificação é registrada.

---

## 🔧 Trocando o mecanismo de persistência

O `index.php` já traz (comentado) um seletor de driver por linha de comando.
Para alternar entre memória, JSON e MySQL basta descomentar o bloco `match`:

```php
$driver = $argv[1] ?? 'json';
$container = match ($driver) {
    'memoria' => Container::comMemoria(),
    'json'    => Container::comJson(__DIR__ . '/../data'),
    'mysql'   => Container::comMySQL(new \PDO(
        getenv('DB_DSN')  ?: 'mysql:host=localhost;dbname=loja_online;charset=utf8mb4',
        getenv('DB_USER') ?: 'root',
        getenv('DB_PASS') ?: ''
    )),
    default   => throw new \InvalidArgumentException("Driver desconhecido: \"{$driver}\"."),
};
```

Nenhuma regra de negócio muda — esse é justamente o ganho da arquitetura hexagonal.

---

## 🛡️ Regras de negócio garantidas pelo domínio

As invariantes são protegidas por exceções de domínio ([`Domain/Exception/`](src/Domain/Exception/)):

| Regra | Exceção |
|-------|---------|
| Não adicionar o mesmo produto duas vezes ao carrinho | `ProdutoJaEstaNoCarrinho` |
| Remover item que não está no carrinho | `ProdutoNaoEstaNoCarrinho` |
| Calcular total de carrinho vazio | `CarrinhoVazioNaoPossuiTotal` |
| Dar baixa sem estoque suficiente | `EstoqueInsuficiente` |
| Dinheiro negativo | `ValorMonetarioNaoPodeSerNegativo` |
| Quantidade negativa | `QuantidadeNaoPodeSerNegativa` |
| Baixa de quantidade ≤ 0 | `QuantidadeParaBaixaDeveSerSuperiorAZero` |
| E-mail inválido | `EmailInvalido` |
| Carrinho / produto inexistente | `CarrinhoNaoEncontrado` / `ProdutoNaoEncontrado` |

---

## 🧠 Decisões de design

- **Dinheiro em centavos (inteiro):** elimina imprecisão de ponto flutuante.
- **Value Objects autovalidados:** estados inválidos são impossíveis de instanciar.
- **Checkout em duas fases:** validação total antes de qualquer efeito colateral.
- **Composition Root único:** toda a "fiação" das dependências num só lugar.
- **PSR-4 / namespace** `Tavares\LojaOnline\` mapeado para `src/`.
- **Sem dependências de terceiros:** Composer usado apenas para o autoload.

---

## 👤 Autor

**Rodrigo Cesar Tavares Ferreira**

Projeto de estudo de Arquitetura Hexagonal (Ports & Adapters) em PHP.
