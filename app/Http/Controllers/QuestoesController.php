<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alternativa;
use App\Models\Questao;
use App\Models\UsuarioResposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestoesController extends Controller
{
    // admin
    public function criaQuestao(Request $request)
    {
        // return response()->json(['message' => $request->questoes]);
        $validator = Validator::make($request->all(), [
            'questoes.titulo' => 'required|string|max:900',
            'questoes.dificuldade' => 'required|string|max:50',
            'questoes.categoria' => 'required|string|max:100',
            'alternativas' => 'required|array|min:2',
            'alternativas.*.texto' => 'required|string|max:500',
            'alternativas.*.correta' => 'required|boolean'
        ], [
            'questoes.titulo.required' => 'Título não informado',
            'questoes.dificuldade.required' => 'Dificuldade não informada',
            'questoes.categoria.required' => 'Categoria não informada',
            'alternativas.required' => 'Nenhuma alternativa foi enviada',
            'alternativas.min' => 'É necessário pelo menos :min alternativas',
            'alternativas.*.texto.required' => 'Texto da alternativa é obrigatório',
            'alternativas.*.correta.required' => 'Indicação de correção não fornecida'
        ]);
        // verificar se existe ao menos uma alternativa correta
        $validator->after(function ($validator) use ($request) {
            $temAlternativaCorreta = collect($request->alternativas)->contains('correta', true);

            if (!$temAlternativaCorreta) {
                $validator->errors()->add(
                    'alternativas',
                    'Pelo menos uma alternativa deve estar marcada como correta'
                );
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $questao = Questao::create([
                'titulo' => $request->questoes['titulo'],
                'dificuldade' => $request->questoes['dificuldade'],
                'categoria' => $request->questoes['categoria'],
            ]);

            $alternativasData = [];

            foreach ($request->alternativas as $alternativa) {
                $alternativasData[] = [
                    'texto' => $alternativa['texto'],
                    'correta' => $alternativa['correta'],
                    'questao_id' => $questao->id,
                ];
            }

            Alternativa::insert($alternativasData);

            return response()->json([
                'message' => 'Questão e alternativas criadas com sucesso',
                // 'questao_id' => $questao->id
            ], 201);
        } catch (\Exception $e) {
            return false;
        }

        if ($request->questoes && $request->alternativas) {
            $request->questoes->validate([
                'titulo' => 'required',
                'dificuldade' => 'required',
                'categoria' => 'required'
            ], [
                'titulo.required' => 'Título não informado',
                'dificuldade.required' => 'Dificuldade não informada',
                'categoria.required' => 'Categoria não informada'
            ]);
            $request->alternativas->validate([
                'texto' => 'required'
            ]);
            $questao = Questao::create([
                'titulo' => $request->questao->titulo,
                'dificuldade' => $request->questao->dificuldade,
                'categoria' => $request->questao->categoria,
            ]);
            $alternativa = Alternativa::create([
                'texto' => $request->alternativa->texto,
                'questao_id' => $questao->id
            ]);

            return response()->json(['message' => 'Questão e alternativa criadas com sucesso']);

        } else if ($request->questoes && !$request->alternativas) {
            return response()->json(['message' => 'Nenhuma alternativa foi enviada'], 400);
        } else {
            return response()->json(['message' => 'Dados insuficientes'], 400);
        }
    }
    public function getQuestoes()
    {
        $questoes = Questao::all();
        return response()->json($questoes, 200);
    }
    public function getQuestao(Questao $questao)
    {
        $alternativas = $questao->alternativas;
        return response()->json($questao, 200);
    }
    public function respondeQuestao(Request $request){
        $validator = Validator::make([
            'usuario_id' => 'required',
            'questao_id' => 'required',
            'alternativa_selecionada_id' => 'required',
        ], [
            'usuario_id.required' => 'Usuário não identificado',
            'questao_id.required' => 'Questão não identificada',
            'alternativa_selecionada_id.required' => 'Nenhuma alternativa selecionada'
        ]);

        UsuarioResposta::create([
            'usuario_id' => $request->usuario_id,
            'questao_id' => $request->questao_id,
            'alternativa_selecionada_id' => $request->alternativa_selecionada_id,
        ]);

        return response()->json(['message' => 'Resposta registrada', 200]);
    }
}
