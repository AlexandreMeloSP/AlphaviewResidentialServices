<?php

/**
 * Helpers globais do projeto Alphaview.
 *
 * Este arquivo contém funções utilitárias reutilizáveis para evitar
 * duplicação de lógica comum entre controllers e models.
 */

if (! function_exists('sanitizeString')) {
    /**
     * Remove tags HTML e escapa entidades para prevenir XSS.
     *
     * Combina strip_tags() para remover tags com htmlspecialchars()
     * para escapar caracteres especiais. Usado em todos os campos
     * de entrada do usuário que serão exibidos na tela.
     *
     * @param  string  $value  Valor bruto recebido do formulário
     * @return string  Valor sanitizado pronto para exibição
     */
    function sanitizeString(string $value): string
    {
        return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('validateImageFile')) {
    /**
     * Valida se um arquivo uploadado é uma imagem válida.
     *
     * Verifica o conteúdo real do arquivo usando getimagesize()
     * (não apenas a extensão) para impedir uploads maliciosos.
     * Retorna null se válido, ou JsonResponse de erro 422 se inválido.
     *
     * @param  \Illuminate\Http\UploadedFile  $file  Arquivo a validar
     * @return \Illuminate\Http\JsonResponse|null  null se OK, ou resposta de erro
     */
    function validateImageFile(\Illuminate\Http\UploadedFile $file): ?\Illuminate\Http\JsonResponse
    {
        $imageInfo = @getimagesize($file->getRealPath());

        if ($imageInfo === false) {
            return response()->json([
                'message' => 'Arquivo de imagem inválido.',
            ], 422);
        }

        return null;
    }
}

if (! function_exists('setPaginationPrefix')) {
    /**
     * Ajusta a URL de paginação para incluir o prefixo de rota.
     *
     * O Laravel gera URLs de paginação relativas à raiz, mas como
     * o app roda em /alphaview, precisamos adicionar o prefixo.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator  $paginator  Instância de paginação
     * @param  string  $path  Caminho da rota (ex: '/api/servicos')
     * @return void
     */
    function setPaginationPrefix(\Illuminate\Pagination\LengthAwarePaginator $paginator, string $path): void
    {
        $prefix = config('app.route_prefix');

        if ($prefix) {
            $paginator->setPath("/{$prefix}{$path}");
        }
    }
}
