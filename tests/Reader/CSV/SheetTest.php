<?php

namespace OpenSpout\Reader\CSV;

use OpenSpout\TestUsingResource;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SheetTest extends TestCase
{
    use TestUsingResource;

    public function testReaderShouldReturnCorrectSheetInfos(): void
    {
        $sheet = $this->openFileAndReturnSheet('csv_standard.csv');

        static::assertSame('', $sheet->getName());
        static::assertSame(0, $sheet->getIndex());
        static::assertTrue($sheet->isActive());
    }

    private function openFileAndReturnSheet(string $fileName): Sheet
    {
        $resourcePath = $this->getResourcePath($fileName);
        $reader = Reader::factory();
        $reader->open($resourcePath);

        $sheet = $reader->getSheetIterator()->current();

        $reader->close();

        return $sheet;
    }
}
