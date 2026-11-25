# owner888/php-http

Zero-dependency pure-PHP 8.3+ coroutine event loop (`stream_select` / PHP 8.6 `Io\Poll` + `Fiber`), non-blocking DNS and TLS sockets, HTTP/1.1 & HTTP/2 client (ALPN negotiation, HPACK header compression, multiplexed streams, connection pools, gzip decompression, HTTP `CONNECT` & SOCKS5 proxies, chunked upload progress), and WebSocket client.

Shared by [MyPHP](https://github.com/owner888/myphp) and [pig](https://github.com/owner888/pig). No `ext-curl` or C extension required beyond PHP's standard `ext-openssl`.

## Installation

```bash
composer require owner888/php-http
```

## Quick Start

### Concurrent HTTP/2 Requests with Fibers

```php
<?php
use Owner888\Async\Async;
use Owner888\Http\{HttpClient, Request};

Async::run(function (): void {
    $client = new HttpClient(timeout: 15.0);

    // 8 concurrent requests multiplexed over a single HTTP/2 connection:
    $futures = [];
    for ($i = 0; $i < 8; $i++) {
        $futures[] = Async::spawn(function () use ($client, $i): string {
            $res = $client->follow(new Request(
                'GET',
                "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=5m&limit=1000",
                ['Accept-Encoding' => 'gzip'],
            ));
            return $res->body->all();
        });
    }

    foreach ($futures as $f) {
        $body = $f->await();
        echo strlen($body), " bytes\n";
    }
});
```

### Embedding inside a GUI Event Loop (`Loop::turn()`)

When a host framework (such as a native GUI run loop) owns the main thread, drive the loop without blocking via `Loop::turn()`:

```php
use Owner888\Async\Loop;

// Inside your 2ms GUI timer callback while requests are in flight:
Loop::get()->turn();
```

### Proxies & Custom CA Certificates

```php
use Owner888\Async\Socket;
use Owner888\Http\{HttpClient, Proxy};

// Honor HTTP_PROXY / HTTPS_PROXY / ALL_PROXY / NO_PROXY:
$client = new HttpClient(proxy: Proxy::fromEnvironment(getenv()));

// Or explicit HTTP / SOCKS5 proxy:
$client = new HttpClient(proxy: Proxy::parse('socks5://127.0.0.1:1080'));

// Trust an additional CA certificate (or set SSL_CERT_FILE in the environment):
Socket::useExtraCaFile('/path/to/custom-ca.pem');
```

### WebSocket Client

```php
use Owner888\Async\Async;
use Owner888\Http\WebSocket;

Async::run(function (): void {
    $ws = WebSocket::connect('wss://echo.websocket.events', []);
    $ws->send('hello');
    echo $ws->receive(), "\n";
    $ws->close();
});
```

## License

MIT
