<?php
[, $replyFile, $logFile, $port] = $argv;
$replies = json_decode(file_get_contents($replyFile), true);
$server = stream_socket_server('tcp://127.0.0.1:' . $port, $errno, $errstr);
echo parse_url(stream_socket_get_name($server, false), PHP_URL_PORT) ?: explode(':', stream_socket_get_name($server, false))[1], "\n";
fflush(STDOUT);

$i = 0;
while ($conn = @stream_socket_accept($server, -1)) {
    $head = '';
    while (!str_contains($head, "\r\n\r\n")) {
        $chunk = fread($conn, 8192);
        if ($chunk === '' || $chunk === false) {
            break;
        }
        $head .= $chunk;
    }
    [$headText, $body] = explode("\r\n\r\n", $head, 2) + [1 => ''];
    $lines = explode("\r\n", $headText);
    [$method, $path] = explode(' ', array_shift($lines));
    $headers = [];
    foreach ($lines as $line) {
        [$name, $value] = explode(':', $line, 2);
        $headers[strtolower($name)] = trim($value);
    }
    $length = (int)($headers['content-length'] ?? 0);
    while (strlen($body) < $length) {
        $body .= fread($conn, $length - strlen($body));
    }
    file_put_contents($logFile, json_encode(['method' => $method, 'path' => $path, 'headers' => $headers, 'headerLines' => $lines, 'body' => $body]) . "\n", FILE_APPEND);

    $reply = $replies[min($i++, count($replies) - 1)];
    if (isset($reply['proxyTo'])) {
        $proxied = proxy($reply['proxyTo'], $method, $path, $headers, $body);
        if (!isset($reply['drop'])) {
            fwrite($conn, $proxied);
        }
    } elseif (isset($reply['hang'])) {
        sleep(2);
    } elseif (!isset($reply['drop'])) {
        $out = "HTTP/1.1 {$reply['status']} Fake\r\n";
        foreach ($reply['headers'] as $name => $value) {
            $out .= "$name: $value\r\n";
        }
        $out .= 'Content-Length: ' . ($reply['contentLength'] ?? strlen($reply['body'])) . "\r\nConnection: close\r\n\r\n" . $reply['body'];
        fwrite($conn, $out);
    }
    fclose($conn);
}

function proxy(string $target, string $method, string $path, array $headers, string $body): string
{
    $forwarded = [];
    foreach (['authorization', 'content-type', 'idempotency-key', 'x-workspace-key'] as $name) {
        if (isset($headers[$name])) {
            $forwarded[] = "$name: {$headers[$name]}";
        }
    }
    $responseHeaders = [];
    $curl = curl_init($target . $path);
    curl_setopt_array($curl, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $forwarded,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADERFUNCTION => function ($curl, $line) use (&$responseHeaders) {
            $parts = explode(':', $line, 2);
            if (count($parts) == 2) {
                $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
            return strlen($line);
        },
    ]);
    $responseBody = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $out = "HTTP/1.1 $status Proxied\r\n";
    foreach (['content-type', 'idempotency-key-status'] as $name) {
        if (isset($responseHeaders[$name])) {
            $out .= "$name: {$responseHeaders[$name]}\r\n";
        }
    }
    return $out . 'Content-Length: ' . strlen($responseBody) . "\r\nConnection: close\r\n\r\n" . $responseBody;
}
