<?php

/**
 * Shared setup for the example scripts.
 *
 * Loads Composer's autoloader and defines show(), which prints a titled value
 * as pretty JSON so the output is easy to read and to compare.
 */

$autoload = __DIR__ . '/../vendor/autoload.php';

if (!is_file($autoload)) {
    fwrite(STDERR, "Dependencies are missing. Run \"composer install\" in the project root first.\n");
    exit(1);
}

require $autoload;

/**
 * Prints a section title followed by the value as pretty-printed JSON.
 *
 * @param string $title Section title
 * @param mixed  $value Value to print
 */
function show(string $title, $value): void
{
    echo '== ' . $title . ' ==' . PHP_EOL;
    echo json_encode(
        $value,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
    ) . PHP_EOL . PHP_EOL;
}
