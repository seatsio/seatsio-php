<?php
[, $replyFile, $logFile] = $argv;
$replies = json_decode(file_get_contents($replyFile), true);
$server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
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
    file_put_contents($logFile, json_encode(['method' => $method, 'path' => $path, 'headers' => $headers, 'body' => $body]) . "\n", FILE_APPEND);

    $reply = $replies[min($i++, count($replies) - 1)];
    if (!isset($reply['drop'])) {
        $out = "HTTP/1.1 {$reply['status']} Fake\r\n";
        foreach ($reply['headers'] as $name => $value) {
            $out .= "$name: $value\r\n";
        }
        $out .= 'Content-Length: ' . ($reply['contentLength'] ?? strlen($reply['body'])) . "\r\nConnection: close\r\n\r\n" . $reply['body'];
        fwrite($conn, $out);
    }
    fclose($conn);
}
