<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FontteService
{
    private string $token;
    private string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    public function send(string $target, string $message): array
    {
        $target = $this->formatPhone($target);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->asForm()->post($this->apiUrl, [
                'target'  => $target,
                'message' => $message,
                'delay'   => 0,
            ]);

            return $response->json() ?? ['status' => false, 'reason' => 'No response from Fonnte'];
        } catch (\Exception $e) {
            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
