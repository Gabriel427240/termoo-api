<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;
use Illuminate\Support\Str;

class JogoController extends Controller
{
    // INICIAR JOGO
    public function iniciarJogo()
    {
        $todasAsPalavras = config('dicionario');

        $palavraSorteada = $todasAsPalavras[array_rand($todasAsPalavras)];

        $meuIdUnico = Str::uuid()->toString();

        Jogo::create([
            'id' => $meuIdUnico,
            'palavra_secreta' => $palavraSorteada,
            'tentativas_restantes' => 6
        ]);

        return response()->json([
            'idJogo' => $meuIdUnico,
            'tamanhoPalavra' => 5,
            'tentativasMaximas' => 6
        ]);
    }

    // VALIDAR TENTATIVA
    public function validarTentativa(Request $request)
    {
        $idJogo = $request->input('idJogo');

        $palavraChutada = strtolower($request->input('palavra'));

        // Buscar jogo
        $jogo = Jogo::find($idJogo);

        if (!$jogo) {
            return response()->json([
                'erro' => 'Jogo não encontrado'
            ], 404);
        }

        // Validar tentativas restantes
        if ($jogo->tentativas_restantes <= 0) {
            return response()->json([
                'erro' => 'Você não tem mais tentativas.'
            ], 400);
        }

        // Validar palavra
        $todasAsPalavras = config('dicionario');

        if (
            strlen($palavraChutada) !== 5 ||
            !in_array($palavraChutada, $todasAsPalavras)
        ) {
            return response()->json([
                'palavraValida' => false,
                'erro' => 'Palavra inválida. Precisa ter 5 letras e existir no dicionário.'
            ], 400);
        }

        // Comparar letras
        $letrasSecretas = str_split($jogo->palavra_secreta);

        $letrasChutadas = str_split($palavraChutada);

        $resultado = [];

        $acertos = 0;

        // Inicializa tudo como ausente
        for ($i = 0; $i < 5; $i++) {

            $resultado[$i] = [
                'letra' => $letrasChutadas[$i],
                'status' => 'ausente'
            ];
        }

        // Letras corretas
        for ($i = 0; $i < 5; $i++) {

            if ($letrasChutadas[$i] === $letrasSecretas[$i]) {

                $resultado[$i]['status'] = 'correta';

                $letrasSecretas[$i] = '*';

                $acertos++;
            }
        }

        // Letras presentes
        for ($i = 0; $i < 5; $i++) {

            if ($resultado[$i]['status'] === 'ausente') {

                $posicao = array_search(
                    $letrasChutadas[$i],
                    $letrasSecretas
                );

                if ($posicao !== false) {

                    $resultado[$i]['status'] = 'presente';

                    $letrasSecretas[$posicao] = '*';
                }
            }
        }

        // Atualiza tentativas
        $jogo->tentativas_restantes -= 1;

        $jogo->save();

        // Verifica vitória
        $venceu = ($acertos === 5);

        // Resposta final
        return response()->json([
            'resultado' => $resultado,
            'venceu' => $venceu,
            'tentativasRestantes' => $jogo->tentativas_restantes,
            'palavraValida' => true
        ]);
    }
}