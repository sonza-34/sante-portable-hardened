<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_BASE_URL', 'http://localhost:11434');
        $this->model = env('OLLAMA_MODEL', 'qwen2.5:7b');
        $this->timeout = (int) env('OLLAMA_TIMEOUT', 60);
    }

    /**
     * @param  array<int,array{role:string,content:string}>  $history
     * @return array{success:bool,reply:string,duration_ms?:int|null}
     */
    public function chat(string $systemPrompt, string $userMessage, array $history = []): array
    {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout(5)
                ->post("{$this->baseUrl}/api/chat", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'stream' => false,
                    'options' => ['temperature' => 0.4, 'num_predict' => 500],
                ]);

            if ($response->failed()) {
                Log::channel('medical')->warning('ollama.http_error', [
                    'status' => $response->status(),
                    'model' => $this->model,
                ]);
                return [
                    'success' => false,
                    'reply' => "Le service IA est momentanément indisponible. "
                        . "Veuillez réessayer dans quelques instants ou contactez votre médecin via la messagerie de l'app.",
                ];
            }

            $data = $response->json();
            return [
                'success' => true,
                'reply' => trim($data['message']['content'] ?? ''),
                'duration_ms' => $data['total_duration'] ?? null,
            ];
        } catch (\Throwable $e) {
            // Log SANS contenu du message (RGPD — pas de fuite de donnée santé dans les logs)
            Log::channel('medical')->error('ollama.unreachable', [
                'error' => $e->getMessage(),
                'base_url' => $this->baseUrl,
                'model' => $this->model,
            ]);
            return [
                'success' => false,
                'reply' => "Le service IA est injoignable. "
                    . "Vérifiez votre connexion ou réessayez plus tard. "
                    . "Si c'est urgent, appelez le **15** (SAMU Maroc) ou le **112**.",
            ];
        }
    }

    public function isAvailable(): bool
    {
        try {
            return Http::timeout(2)->get($this->baseUrl)->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
