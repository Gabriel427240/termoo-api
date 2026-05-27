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
        $jogo = Jogo::findOrFail($id);

        return response()->json($jogo, 200);
    }

    // Cria um jogo manualmente
    public function store(Request $request)
    {
        return response()->json([
            'success' => true,
            'mensagem' => 'POST funcionando',
            'dados_recebidos' => $request->all()
        ], 200);
    }

    // Atualiza um jogo
    public function update(Request $request, $id)
    {
        $jogo = Jogo::findOrFail($id);

        $jogo->update($request->all());

        return response()->json($jogo, 200);
    }

    // Apaga um jogo
    public function destroy($id)
    {
        $jogo = Jogo::findOrFail($id);

        $jogo->delete();

        return response()->json(null, 204);
    }

    // Inicia jogo sorteando palavra
    public function iniciarJogo()
    {
        $dicionario = config('dicionario');

        $palavra = $dicionario[array_rand($dicionario)];

        $jogo = Jogo::create([
            'id' => uniqid(),
            'palavra_secreta' => $palavra,
            'tentativas_restantes' => 6
        ]);

        return response()->json($jogo, 201);
    }

    // Valida tentativa
    public function validarTentativa(Request $request, $idJogo)
    {
        $jogo = Jogo::findOrFail($idJogo);

        $palpite = $request->input('palpite');

        if ($palpite === $jogo->palavra_secreta) {

            return response()->json([
                'mensagem' => 'Parabéns, você acertou!',
                'jogo' => $jogo
            ], 200);
        }

        $jogo->tentativas_restantes -= 1;

        $jogo->save();

        return response()->json([
            'mensagem' => 'Palpite errado!',
            'tentativas_restantes' => $jogo->tentativas_restantes
        ], 200);
    }
}