<?php

namespace Dalue\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Runs every example script and compares its output with examples/output/<name>.txt.
 *
 * Regenerate an expected output after an intentional change with:
 * php examples/<name>.php > examples/output/<name>.txt
 */
final class ExamplesTest extends TestCase
{
    private const EXAMPLES_DIR = __DIR__ . '/../examples';

    /**
     * @return array<string, array{string}>
     */
    public function exampleProvider(): array
    {
        $cases = [];

        foreach (glob(self::EXAMPLES_DIR . '/[0-9][0-9]-*.php') ?: [] as $file) {
            $cases[basename($file, '.php')] = [$file];
        }

        return $cases;
    }

    public function testEveryExampleHasAnExpectedOutput(): void
    {
        $examples = array_keys($this->exampleProvider());
        $outputs = array_map(function (string $file): string {
            return basename($file, '.txt');
        }, glob(self::EXAMPLES_DIR . '/output/*.txt') ?: []);

        $this->assertNotEmpty($examples);
        $this->assertSame($examples, $outputs);
    }

    /**
     * @dataProvider exampleProvider
     */
    public function testExampleOutputMatches(string $file): void
    {
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($file) . ' 2>&1';
        exec($command, $lines, $exitCode);

        $output = implode("\n", $lines);
        $expected = file_get_contents(self::EXAMPLES_DIR . '/output/' . basename($file, '.php') . '.txt');

        $this->assertSame(0, $exitCode, $output);
        $this->assertSame(rtrim((string) $expected), rtrim($output));
    }
}
