<?php

namespace App\Http\Controllers;

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
        })->first();

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

        
        $word = Word::create([
            'word' => $word_string,
            'length' => strlen($word_string),
            'definition' => $word_string,
            'is_verified' => true,
            'is_valid' => true,
        ]);

        if ($word->is_valid) {
            return response()->json([
                'success' => true,
                'message' => 'Palavra válida.',
                'data' => [
                    'palavra' => $word->word,
                    'definicao' => $word->definition,
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Palavra inválida.',
                'data' => []
            ], 404);
        }
    }
}

