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
            'questoes.titulo' => 'required|string|max:1500',
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
       try{
           $usuarioId = auth()->id();

           $questoes = Questao::with('alternativas')
           ->naoRespondidasPor($usuarioId)
           ->inRandomOrder()
           ->get();

           if($questoes->isEmpty()){
            return response()->json([
                'message' => 'Você já respondeu todas as questões disponíveis!'
            ], 404);
           }

           return response()->json($questoes,200);
       } catch(\Exception $e){
        return response()->json([
            'message' => 'Erro ao buscar questões'
        ], 500);
       }
        
        // $questoes = Questao::all();
        // return response()->json($questoes, 200);
    }
    public function getQuestao(Questao $questao)
    {
        $alternativas = $questao->alternativas;
        return response()->json($questao, 200);
    }
    public function getRandomQuestao()
    {
        try {
            $randomQuestoes = Questao::with(['alternativas' => function($query){
                $query->select('id', 'questao_id', 'texto', 'correta');
            }])
            ->select('id', 'titulo', 'dificuldade', 'categoria')
            ->inRandomOrder()
            ->take(5)
            ->get();

            if ($randomQuestoes->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma questão encontrada'
                ], 404);
            }

            return response()->json($randomQuestoes, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar questões'
            ], 500);
        }
    }
    public function respondeQuestao(Request $request)
    {
        $usuario = auth()->user();

        if(!$usuario){
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        $validator = Validator::make($request->all(), [
            'questao_id' => 'required|exists:questoes,id',
            'alternativa_selecionada_id' => 'required|exists:alternativas,id',
        ], [
            'questao_id.required' => 'Questão não identificada',
            'alternativa_selecionada_id.required' => 'Nenhuma alternativa selecionada',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // verifica se já respondeu antes
        $respExistente = UsuarioResposta::where('usuario_id', $usuario->id)
        ->where('questao_id', $request->questao_id)
        ->first();

        if($respExistente){
            return response()->json([
                'message' => 'Você já respondeu esta questão anteriormente',
                'correta' => $respExistente->alternativaSelecionada->correta ?? false
            ], 409);
        }

        $alt = $request->alternativa_selecionada_id;

        // verifica se alternativa pertence à questão
        $alternativaValida = Alternativa::where('id', $alt)
        ->where('questao_id', $request->questao_id)
        ->exists();

        if(!$alternativaValida){
            return response()->json([
                'message' => 'Alternativa não pertence à questão selecionada'
            ], 422);
        }

        $isCorreta = Alternativa::where('id', $alt)->where('correta', true)->exists();
        
        try {
            UsuarioResposta::create([
                'usuario_id' => $usuario->id, //id do usuário autenticado
                'questao_id' => $request->questao_id,
                'alternativa_selecionada_id' => $request->alternativa_selecionada_id,
                // 'answered_at' => now();
            ]);
            // retornar feedback para o usuário
            return response()->json([
                'message' => $isCorreta ? 'Resposta correta!' : 'Resposta incorreta!', 
                'correta' => $isCorreta
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao responder.', 
                // RETIRAR EM PRODUÇÃO
                'error' => $e->getMessage()
        ], 500);
        }

    }
}
