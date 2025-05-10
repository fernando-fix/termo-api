<?php

namespace App\Http\Controllers;

use App\Helpers\ChatGpt;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WordController extends Controller
{
    public function random(Request $request)
    {
        $word = Word::inRandomOrder()->where(function ($query) use ($request) {
            if ($request->has('min_length')) {
                $query->where('length', '>=', $request->min_length);
            }
            if ($request->has('max_length')) {
                $query->where('length', '<=', $request->max_length);
            }
        })->where('is_valid', true)
            ->first();

        if (!$word) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma palavra encontrada.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Requisição bem-sucedida.',
            'data' => [
                'palavra' => $word->word,
                'definicao' => $word->definition,
                'tamanho' => $word->length
            ]
        ], 200);
    }

    public function check(Request $request)
    {
        $word = Word::where('word', $request->palavra)->first();
        $word_string = $request->palavra;


        if ($word) {
            return response()->json([
                'success' => $word->is_valid,
                'message' => $word->is_valid ? 'Palavra válida.' : 'Palavra inválida.',
                'data' => $word->is_valid ? [
                    'palavra' => $word->word,
                    'definicao' => $word->definition,
                    'tamanho' => $word->length
                ] : []
            ], 200);
        }

        $gpt = new ChatGpt();
        $json = $gpt->chat(sprintf("
            Para esta exata palavra %s,
            Não retornar ```, apenas retornar json
            Não retornar uma descrição, apenas retornar json
            Verifique se a palavra %s é uma palavra real, e se ela tem uma definição. Se ela for uma palavra real, escreva a definição dela e a palavra em inglês.
            {
                'is_valid': true,
                'definition': 'Definição da palavra'
            }

            ou

            {
                'is_valid': false
            }
        ", addslashes($word_string), addslashes($word_string)));

        $gptData = json_decode($json, true);

        if (isset($gptData['is_valid']) && ($gptData['is_valid'] || !$gptData['is_valid'])) {
            $createWord = Word::create([
                'word' => $word_string,
                'length' => strlen($word_string),
                'definition' => $gptData['definition'] ?? null,
                'is_verified' => true,
                'is_valid' => $gptData['is_valid'],
            ]);
        }

        if (isset($gptData['is_valid']) && !$gptData['is_valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Palavra inválida.',
                'data' => []
            ], 200);
        }

        if (isset($createWord)) {
            return response()->json([
                'success' => true,
                'message' => 'Palavra válida.',
                'data' => [
                    'palavra' => $createWord->word,
                    'definicao' => $createWord->definition,
                    'tamanho' => $createWord->length,
                    'created' => $createWord ? true : false
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhuma palavra encontrada.',
            'data' => [
                'gptData' => json_encode($gptData)
            ],
        ], 200);
    }
}
