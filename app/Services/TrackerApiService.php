<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackerApiService
{
    private string $baseUrl;
    private ?string $email;
    private ?string $password;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.tracker.url', ''), '/');
        $this->email    = config('services.tracker.email') ?: null;
        $this->password = config('services.tracker.password') ?: null;
    }

    public function isConfigured(): bool
    {
        return $this->email !== null && $this->password !== null;
    }

    private function fetchToken(): ?string
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/api/login", [
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            return $response->successful() ? $response->json('token') : null;
        } catch (\Throwable $e) {
            Log::warning('TrackerApiService: login failed — ' . $e->getMessage());
            return null;
        }
    }

    private function getToken(): ?string
    {
        $cached = Cache::get('tracker_api_token');
        if ($cached) return $cached;

        $token = $this->fetchToken();
        if ($token) {
            Cache::put('tracker_api_token', $token, now()->addHour());
        }
        return $token;
    }

    private function forgetToken(): void
    {
        Cache::forget('tracker_api_token');
    }

    /**
     * Return task completion statistics per period (bulan/tahun), keyed by user email.
     */
    public function getStatisticsByEmailForPeriod(int $bulan, int $tahun): array
    {
        if (! $this->isConfigured()) return [];

        $token = $this->getToken();
        if (! $token) return [];

        $data = $this->requestFromEndpoint($token, "/api/statistics-period?bulan={$bulan}&tahun={$tahun}");

        if ($data === null) {
            $this->forgetToken();
            $token = $this->getToken();
            if (! $token) return [];
            $data = $this->requestFromEndpoint($token, "/api/statistics-period?bulan={$bulan}&tahun={$tahun}");
        }

        return $this->indexByEmail($data ?? []);
    }

    /**
     * Return all-time statistics keyed by user email (fallback).
     */
    public function getStatisticsByEmail(): array
    {
        if (! $this->isConfigured()) return [];

        $token = $this->getToken();
        if (! $token) return [];

        $data = $this->requestFromEndpoint($token, '/api/statistics');

        if ($data === null) {
            $this->forgetToken();
            $token = $this->getToken();
            if (! $token) return [];
            $data = $this->requestFromEndpoint($token, '/api/statistics');
        }

        return $this->indexByEmail($data ?? []);
    }

    /**
     * Aggregate total_task and task_completed across ALL users in Tracker (all-time).
     * Returns ['connected' => bool, 'total_task' => int, 'task_completed' => int]
     */
    public function getAggregatedStats(): array
    {
        if (! $this->isConfigured()) {
            return ['connected' => false, 'total_task' => 0, 'task_completed' => 0];
        }

        $token = $this->getToken();
        if (! $token) {
            return ['connected' => false, 'total_task' => 0, 'task_completed' => 0];
        }

        $data = $this->requestFromEndpoint($token, '/api/statistics');

        if ($data === null) {
            $this->forgetToken();
            $token = $this->getToken();
            if (! $token) return ['connected' => false, 'total_task' => 0, 'task_completed' => 0];
            $data = $this->requestFromEndpoint($token, '/api/statistics');
        }

        if ($data === null) {
            return ['connected' => false, 'total_task' => 0, 'task_completed' => 0];
        }

        $totalTask = 0;
        $taskCompleted = 0;
        foreach ($data as $stat) {
            $totalTask     += (int) ($stat['total_todo']   ?? 0)
                            + (int) ($stat['total_doing']  ?? 0)
                            + (int) ($stat['total_done']   ?? 0);
            $taskCompleted += (int) ($stat['total_done']   ?? 0);
        }

        return ['connected' => true, 'total_task' => $totalTask, 'task_completed' => $taskCompleted];
    }

    /**
     * Get all-time statistics for a single user matched by email.
     * Returns ['connected' => bool, 'total_task' => int, 'task_completed' => int]
     */
    public function getSingleStatByEmail(string $email): array
    {
        $empty = ['connected' => false, 'total_task' => 0, 'task_completed' => 0];

        if (! $this->isConfigured()) return $empty;

        $token = $this->getToken();
        if (! $token) return $empty;

        $data = $this->requestFromEndpoint($token, '/api/statistics');

        if ($data === null) {
            $this->forgetToken();
            $token = $this->getToken();
            if (! $token) return $empty;
            $data = $this->requestFromEndpoint($token, '/api/statistics');
        }

        foreach ($data ?? [] as $stat) {
            if (($stat['user']['email'] ?? null) === $email) {
                $total = (int) ($stat['total_todo']  ?? 0)
                       + (int) ($stat['total_doing'] ?? 0)
                       + (int) ($stat['total_done']  ?? 0);
                return [
                    'connected'      => true,
                    'total_task'     => $total,
                    'task_completed' => (int) ($stat['total_done'] ?? 0),
                ];
            }
        }

        return $empty;
    }

    private function indexByEmail(array $data): array
    {
        $indexed = [];
        foreach ($data as $stat) {
            $email = $stat['user']['email'] ?? null;
            if ($email) {
                $indexed[$email] = $stat;
            }
        }
        return $indexed;
    }

    private function requestFromEndpoint(string $token, string $path): ?array
    {
        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->get("{$this->baseUrl}{$path}");

            if (! $response->successful()) return null;

            $body = $response->json();

            if (isset($body['data']) && is_array($body['data'])) {
                return $body['data'];
            }

            if (is_array($body) && array_is_list($body)) {
                return $body;
            }

            return [];
        } catch (\Throwable $e) {
            Log::warning("TrackerApiService: request to {$path} failed — " . $e->getMessage());
            return null;
        }
    }
}
