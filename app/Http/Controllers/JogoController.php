<?php

namespace App\Http\Controllers;

use App\Models\Jogo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JogoController extends Controller
{
    private const TAMANHO_PALAVRA = 5;
    private const TENTATIVAS_MAXIMAS = 6;

    public function iniciarJogo(): JsonResponse
    {
        $jogo = Jogo::create([
            'id' => (string) Str::uuid(),
            'palavra_secreta' => $this->sortearPalavra(),
            'tentativas_restantes' => self::TENTATIVAS_MAXIMAS,
            'venceu' => false,
        ]);

        return response()->json([
            'idJogo' => $jogo->id,
            'tamanhoPalavra' => self::TAMANHO_PALAVRA,
            'tentativasMaximas' => self::TENTATIVAS_MAXIMAS,
        ], 200);
    }

    public function validarTentativa(Request $request): JsonResponse
    {
        $idJogo = $request->input('idJogo');
        $palavra = $this->obterPalavraDaRequisicao($request);

        if (!$idJogo || !$palavra) {
            return $this->erro('Informe idJogo e palavra.', 400);
        }

        return $this->validarPalavra($idJogo, $palavra);
    }

    public function validarTentativaPorJogo(Request $request, string $idJogo): JsonResponse
    {
        $palavra = $this->obterPalavraDaRequisicao($request);

        if (!$palavra) {
            return $this->erro('Informe a palavra.', 400);
        }

        return $this->validarPalavra($idJogo, $palavra);
    }

    private function validarPalavra(string $idJogo, string $palavra): JsonResponse
    {
        $jogo = Jogo::find($idJogo);

        if (!$jogo) {
            return $this->erro('Jogo não encontrado.', 404);
        }

        if (!$this->temCincoLetras($palavra)) {
            return $this->erro('A palavra deve ter exatamente 5 letras.', 400);
        }

        if ($jogo->venceu || $jogo->tentativas_restantes <= 0) {
            return $this->erro('Esta partida já foi encerrada.', 400);
        }

        $dicionario = $this->carregarDicionario();
        $palavraValida = in_array($palavra, $dicionario, true);

        if (!$palavraValida) {
            return response()->json([
                'resultado' => [],
                'venceu' => false,
                'tentativasRestantes' => $jogo->tentativas_restantes,
                'palavraValida' => false,
            ], 200);
        }

        $resultado = $this->compararPalavras($palavra, $jogo->palavra_secreta);
        $venceu = $palavra === $jogo->palavra_secreta;

        $jogo->tentativas_restantes--;
        $jogo->venceu = $venceu;
        $jogo->save();

        return response()->json([
            'resultado' => $resultado,
            'venceu' => $venceu,
            'tentativasRestantes' => $jogo->tentativas_restantes,
            'palavraValida' => true,
        ], 200);
    }

    private function compararPalavras(string $tentativa, string $palavraSecreta): array
    {
        $letrasTentativa = mb_str_split($tentativa);
        $letrasSecretas = mb_str_split($palavraSecreta);
        $resultado = [];
        $letrasRestantes = [];

        for ($i = 0; $i < self::TAMANHO_PALAVRA; $i++) {
            $resultado[$i] = [
                'letra' => $letrasTentativa[$i],
                'status' => 'ausente',
            ];

            if ($letrasTentativa[$i] === $letrasSecretas[$i]) {
                $resultado[$i]['status'] = 'correta';
            } else {
                $letra = $letrasSecretas[$i];
                $letrasRestantes[$letra] = ($letrasRestantes[$letra] ?? 0) + 1;
            }
        }

        for ($i = 0; $i < self::TAMANHO_PALAVRA; $i++) {
            if ($resultado[$i]['status'] === 'correta') {
                continue;
            }

            $letra = $letrasTentativa[$i];

            if (($letrasRestantes[$letra] ?? 0) > 0) {
                $resultado[$i]['status'] = 'presente';
                $letrasRestantes[$letra]--;
            }
        }

        return $resultado;
    }

    private function carregarDicionario(): array
    {
        $palavras = config('dicionario', []);

        $palavras = array_map(fn ($palavra) => $this->normalizarPalavra($palavra), $palavras);
        $palavras = array_filter($palavras, fn (string $palavra) => $this->temCincoLetras($palavra));

        return array_values(array_unique($palavras));
    }

    private function sortearPalavra(): string
    {
        $dicionario = $this->carregarDicionario();

        return $dicionario[array_rand($dicionario)];
    }

    private function obterPalavraDaRequisicao(Request $request): string
    {
        return $this->normalizarPalavra(
            $request->input('palavra')
                ?? $request->input('tentativa')
                ?? $request->input('palpite')
        );
    }

    private function normalizarPalavra(mixed $palavra): string
    {
        return mb_strtolower(trim((string) $palavra), 'UTF-8');
    }

    private function temCincoLetras(string $palavra): bool
    {
        return mb_strlen($palavra, 'UTF-8') === self::TAMANHO_PALAVRA
            && preg_match('/^[a-záàâãéêíóôõúç]+$/iu', $palavra) === 1;
    }

    private function erro(string $mensagem, int $status): JsonResponse
    {
        return response()->json(['error' => $mensagem], $status);
    }
}