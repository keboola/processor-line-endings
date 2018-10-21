<?php

declare(strict_types=1);

namespace Keboola\ProcessorLineEndings;

use Keboola\Component\BaseComponent;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Component extends BaseComponent
{
    public function run(): void
    {
        $finder = new Finder();
        $finder->in($this->getDataDir() . '/in/tables')->files();
        $this->processDir($finder, 'tables');
        $finder = new Finder();
        $finder->in($this->getDataDir() . '/in/files')->files();
        $this->processDir($finder, 'files');
    }

    private function processDir(Finder $finder, string $dir) : void
    {
        $fs = new Filesystem();
        foreach ($finder as $inFile) {
            if ($inFile->getExtension() === 'manifest') {
                // copy manifest without modification
                $fs->copy($inFile->getPathname(), $this->getDataDir() . "/out/$dir/" . $inFile->getFilename());
            } else {
                $destinationDir = $this->getDataDir() . "/out/$dir/" . $inFile->getRelativePath();
                $fs->mkdir($destinationDir);
                $destinationFile = $destinationDir . '/' . $inFile->getFilename();
                $this->processFile($inFile, $destinationFile);
            }
        }
    }

    private function processFile(SplFileInfo $inFileInfo, string $destinationFileName) : void
    {
        $inFile = fopen($inFileInfo->getPathname(), 'rb');
        if ($inFile === false) {
            throw new \RuntimeException('Cannot open source file: ' . $inFileInfo->getPathname());
        }
        $outFile = fopen($destinationFileName, 'wb');
        if ($outFile === false) {
            throw new \RuntimeException('Cannot open destination file: ' . $destinationFileName);
        }
        while (!feof($inFile)) {
            $line = fread($inFile, 8192);
            if ($line === false) {
                throw new \RuntimeException('Cannot read source file.');
            }
            $line = str_replace("\r", "\n", str_replace("\r\n", "\n", $line));
            if (fwrite($outFile, $line) === false) {
                throw new \RuntimeException('Cannot write destination file.');
            }
        }
    }

    protected function getConfigClass(): string
    {
        return Config::class;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }
}
