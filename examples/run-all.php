<?php

/**
 * Runs every example script in order and prints its output.
 *
 * Run: composer examples
 *  or: php examples/run-all.php
 */

$examples = glob(__DIR__ . '/[0-9][0-9]-*.php') ?: [];
$failed = 0;

foreach ($examples as $file) {
    echo '######## ' . basename($file) . ' ########' . PHP_EOL . PHP_EOL;
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($file), $exitCode);

    if ($exitCode !== 0) {
        $failed++;
        echo 'FAILED with exit code ' . $exitCode . PHP_EOL . PHP_EOL;
    }
}

echo sprintf('%d example(s) run, %d failed.', count($examples), $failed) . PHP_EOL;
exit($failed === 0 ? 0 : 1);
