<?php

namespace Seatsio;

class FakeServer
{
    private $process;
    private string $logFile;
    public int $port;

    public static function replying(array ...$replies): FakeServer
    {
        return new FakeServer($replies);
    }

    public static function json(int $status, string $body): array
    {
        return ['status' => $status, 'headers' => ['Content-Type' => 'application/json'], 'body' => $body];
    }

    public static function error(int $status, string $code): array
    {
        return self::json($status, json_encode(['errors' => [['code' => $code, 'message' => $code]], 'messages' => [$code], 'requestId' => 'req-1']));
    }

    public static function status(int $status): array
    {
        return ['status' => $status, 'headers' => [], 'body' => ''];
    }

    public static function drop(): array
    {
        return ['drop' => true];
    }

    public static function truncated(): array
    {
        return ['status' => 200, 'headers' => ['Content-Type' => 'application/json'], 'body' => '{"par', 'contentLength' => 100];
    }

    private function __construct(array $replies)
    {
        $this->logFile = tempnam(sys_get_temp_dir(), 'fakeserver');
        $replyFile = tempnam(sys_get_temp_dir(), 'fakereplies');
        file_put_contents($replyFile, json_encode($replies));
        $this->process = proc_open(
            [PHP_BINARY, __DIR__ . '/fake_server_process.php', $replyFile, $this->logFile],
            [1 => ['pipe', 'w']],
            $pipes
        );
        $this->port = (int)trim(fgets($pipes[1]));
    }

    public function url(string $path = ''): string
    {
        return 'http://127.0.0.1:' . $this->port . $path;
    }

    public function requests(): array
    {
        return array_map(fn($line) => json_decode($line, true), file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }

    public function sentIdempotencyKeys(): array
    {
        return array_map(fn($r) => $r['headers']['idempotency-key'] ?? null, $this->requests());
    }

    public function close(): void
    {
        proc_terminate($this->process);
        proc_close($this->process);
        @unlink($this->logFile);
    }
}
