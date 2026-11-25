<?php

declare(strict_types=1);

namespace Owner888\Async;

use RuntimeException;

/** A socket that could not connect, negotiate, read or write. */
final class SocketError extends RuntimeException
{
}
