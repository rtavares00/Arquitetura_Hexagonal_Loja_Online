<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\Json;

use Tavares\LojaOnline\Domain\Produto;
use Tavares\LojaOnline\Domain\Exception\ProdutoNaoEncontrado;
use Tavares\LojaOnline\Port\ProdutoRepository;

final class ProdutoRepositoryJson implements ProdutoRepository
{
    use LeituraEscritaJson;

    public function __construct(private string $arquivo)
    {
    }

    public function buscar(int $sku):Produto
    {
        $dados = $this->ler($this->arquivo);

        if(!isset($dados[$sku])):
            throw new ProdutoNaoEncontrado();
        endif;

        return ProdutoJsonMap::deArray($dados[$sku]);
    }

    public function salvar(Produto $produto):void
    {
        $dados = $this->ler($this->arquivo);
        $dados[$produto->getSKU()] = ProdutoJsonMap::paraArray($produto);
        $this->gravar($this->arquivo, $dados);
    }
}
