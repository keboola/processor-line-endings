<?php

declare(strict_types=1);

namespace Keboola\ProcessorLineEndings\FunctionalTests;

use Keboola\DatadirTests\DatadirTestCase;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DatadirTest extends DatadirTestCase
{
    public function assertDirectoryContentsSame(string $expected, string $actual): void
    {
        /* overrides the base method to compare without '--ignore-all-space' option, because we need a
            whitespace sensitive comparison here. */
        $fs = new Filesystem();
        if (!$fs->exists($expected)) {
            throw new AssertionFailedError(sprintf(
                'Expected path "%s" does not exist',
                $expected
            ));
        }
        if (!$fs->exists($actual)) {
            throw new AssertionFailedError(sprintf(
                'Actual path "%s" does not exist',
                $actual
            ));
        }
        $expected = realpath($expected);
        $actual = realpath($actual);
        $diffCommand = [
            'diff',
            '--exclude=.gitkeep',
            '--recursive',
            $expected,
            $actual,
        ];
        $diffProcess = new Process($diffCommand);
        $diffProcess->run();
        if ($diffProcess->getExitCode() > 0) {
            throw new AssertionFailedError(sprintf(
                'Two directories are not the same:' . \PHP_EOL .
                '%s' . \PHP_EOL .
                '%s' . \PHP_EOL .
                '%s' . \PHP_EOL .
                '%s',
                $expected,
                $actual,
                $diffProcess->getOutput(),
                $diffProcess->getErrorOutput()
            ));
        }
    }
}
