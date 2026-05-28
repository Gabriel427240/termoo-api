<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;

class JogoController extends Controller
{
    // Lista todos os jogos
    public function index()
    {
        return response()->json(Jogo::all(), 200);
    }

    // Mostra um jogo específico
    public function show($id)
    {
        $jogo = Jogo::find($id);

        if (!$jogo) {
            return response()->json([
                'success' => false,
                'mensagem' => 'Jogo não encontrado'
            ], 404);
        }

        return response()->json($jogo, 200);
    }

    // Cria um jogo
    public function store(Request $request)
    {
        try {

            $jogo = Jogo::create([
                'id' => uniqid(),
                'palavra_secreta' => 'teste',
                'tentativas_restantes' => 6
            ]);

            return response()->json([
                'success' => true,
                'jogo' => $jogo
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'erro' => $e->getMessage()
            ], 500);
        }
    }

    // Atualiza um jogo
    public function update(Request $request, $id)
    {
        $jogo = Jogo::find($id);

        if (!$jogo) {
            return response()->json([
                'success' => false,
                'mensagem' => 'Jogo não encontrado'
            ], 404);
        }

        $jogo->update($request->all());

        return response()->json([
            'success' => true,
            'jogo' => $jogo
        ], 200);
    }

    // Remove um jogo
    public function destroy($id)
    {
        $jogo = Jogo::find($id);

        if (!$jogo) {
            return response()->json([
                'success' => false,
                'mensagem' => 'Jogo não encontrado'
            ], 404);
        }

        $jogo->delete();

        return response()->json([
            'success' => true,
            'mensagem' => 'Jogo removido com sucesso'
        ], 200);
    }

    // Inicia jogo automaticamente
    public function iniciarJogo()
    {
        try {

            $dicionario = config('dicionario');

            $palavra = $dicionario[array_rand($dicionario)];

            $jogo = Jogo::create([
                'id' => uniqid(),
                'palavra_secreta' => $palavra,
                'tentativas_restantes' => 6
            ]);

            return response()->json([
                'success' => true,
                'jogo' => $jogo
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'erro' => $e->getMessage()
            ], 500);
        }
    }

    // Valida tentativa
    public function validarTentativa(Request $request, $idJogo)
    {
        try {

            $jogo = Jogo::find($idJogo);

            if (!$jogo) {
                return response()->json([
                    'success' => false,
                    'mensagem' => 'Jogo não encontrado'
                ], 404);
            }

            $palpite = $request->input('palpite');

            if (!$palpite) {
                return response()->json([
                    'success' => false,
                    'mensagem' => 'Palpite não enviado'
                ], 400);
            }

            if ($palpite === $jogo->palavra_secreta) {

                return response()->json([
                    'success' => true,
                    'mensagem' => 'Parabéns, você acertou!',
                    'jogo' => $jogo
                ], 200);
            }

            $jogo->tentativas_restantes -= 1;

            $jogo->save();

            return response()->json([
                'success' => false,
                'mensagem' => 'Palpite errado!',
                'tentativas_restantes' => $jogo->tentativas_restantes
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'erro' => $e->getMessage()
            ], 500);
        }
    }
}