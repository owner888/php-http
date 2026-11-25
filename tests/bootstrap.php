<?php
declare(strict_types=1);

$root = dirname(__DIR__);
if (is_file("$root/vendor/autoload.php")) {
    require_once "$root/vendor/autoload.php";
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        foreach ([
            'Owner888\\Async\\' => "$root/src/Async/",
            'Owner888\\Http\\Test\\' => "$root/tests/",
            'Owner888\\Http\\' => "$root/src/Http/",
        ] as $p => $d) {
            if (str_starts_with($class, $p)) {
                $f = $d . str_replace('\\', '/', substr($class, strlen($p))) . '.php';
                if (is_file($f)) {
                    require $f;
                    return;
                }
            }
        }
    });
}
