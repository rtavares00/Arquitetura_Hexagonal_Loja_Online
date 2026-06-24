<?php

declare(strict_types=1);

namespace Tavares\LojaOnline\Adapter\Json;

trait LeituraEscritaJson
{
    private function ler(string $arquivo):array
    {
        if(!file_exists($arquivo)):
            return [];
        endif;

        $conteudo = file_get_contents($arquivo);

        if($conteudo === '' || $conteudo === false):
            return [];
        endif;

        return json_decode($conteudo, true) ?? [];
    }

    private function gravar(string $arquivo, array $dados):void
    {
        $diretorio = dirname($arquivo);

        if(!is_dir($diretorio)):
            mkdir($diretorio, 0777, true);
        endif;

        file_put_contents(
            $arquivo,
            json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
