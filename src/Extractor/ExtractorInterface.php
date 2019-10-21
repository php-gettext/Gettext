<?php
declare(strict_types = 1);

namespace Gettext\Extractor;

interface ExtractorInterface
{
    public function setTranslations(Translations ...$translations): self;

    /**
     * @return Translations[]
     */
    public function getTranslations(): array;

    public function setOptions(array $options): self;

    public function getOptions(): array;

    public function extractFromFile(string $filename): self;

    public function extractfromString(string $string, string $filename = null): self;
}
