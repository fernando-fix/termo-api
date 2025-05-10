<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use App\Models\Config;

class ChatGpt
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    public function chat($message)
    {
        $response = $this->client->post('chat/completions', [
            'json' => [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'user', 'content' => $message],
                ],
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['choices'][0]['message']['content'];
    }
}

