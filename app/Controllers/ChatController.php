<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Campaign;
use App\Models\Location;

class ChatController extends Controller
{
    public function index(array $params): void
    {
        $this->render('public/chat', [
            'title'     => __('chat.title'),
            'locations' => Location::active(),
            'campaigns' => Campaign::active(),
        ]);
    }

    public function send(array $params): void
    {
        $message = trim((string) $this->input('message', ''));
        if ($message === '') {
            $this->json(['reply' => __('chat.welcome')]);
        }

        $reply = $this->generateReply($message);
        $this->json($reply);
    }

    private function generateReply(string $message): array
    {
        $apiKey = (string) config('config.openai.api_key', '');
        $campaigns = Campaign::active();

        // Build a tiny knowledge prompt from campaigns.
        $cards = array_map(static fn ($c) => [
            'name'        => $c['campaign_name'],
            'location'    => $c['location_name'] ?? '',
            'value'       => 'RM ' . number_format((float) $c['voucher_value'], 2),
            'description' => $c['description'] ?? '',
            'claim_url'   => url('/claim/' . $c['id']),
        ], $campaigns);

        if ($apiKey === '') {
            return $this->fallbackReply($message, $cards);
        }

        try {
            $reply = $this->callOpenAI($apiKey, $message, $cards);
            return [
                'reply'      => $reply,
                'campaigns'  => $cards,
            ];
        } catch (\Throwable $e) {
            logger('OpenAI failed: ' . $e->getMessage());
            return $this->fallbackReply($message, $cards);
        }
    }

    private function fallbackReply(string $message, array $cards): array
    {
        $lower = mb_strtolower($message);
        $hits = [];
        foreach ($cards as $c) {
            $hay = mb_strtolower($c['name'] . ' ' . $c['location'] . ' ' . $c['description']);
            foreach (preg_split('/\s+/u', $lower) as $w) {
                if (mb_strlen($w) >= 3 && mb_strpos($hay, $w) !== false) {
                    $hits[] = $c;
                    break;
                }
            }
        }
        if (!$hits) {
            $hits = array_slice($cards, 0, 2);
        }
        $lines = ['Here are some campaigns you may enjoy:'];
        foreach ($hits as $c) {
            $lines[] = sprintf("• %s (%s) — %s", $c['name'], $c['location'], $c['value']);
        }
        return [
            'reply'     => implode("\n", $lines),
            'campaigns' => $hits,
        ];
    }

    private function callOpenAI(string $apiKey, string $userMessage, array $cards): string
    {
        $model = (string) config('config.openai.model', 'gpt-4o-mini');
        $system = "You are SLV's AI Tour Guide for Malaysia tourism. Recommend attractions, food and experiences. "
                . "When relevant, mention available voucher campaigns from the JSON list and tell the user they can claim. "
                . "Be friendly, concise, and reply in the user's language.";
        $payload = [
            'model'    => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'system', 'content' => 'Available campaigns: ' . json_encode($cards, JSON_UNESCAPED_UNICODE)],
                ['role' => 'user',   'content' => $userMessage],
            ],
            'temperature' => 0.7,
            'max_tokens'  => 400,
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT    => 20,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300 || !$body) {
            throw new \RuntimeException("OpenAI HTTP $code");
        }
        $data = json_decode((string) $body, true);
        return (string) ($data['choices'][0]['message']['content'] ?? '');
    }
}
