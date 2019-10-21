<?php
declare(strict_types = 1);

namespace Gettext\Extractor;

interface FunctionsExtractorInterface
{
    /**
     * @return ParsedFunction[]
     */
    public function extractFunctions(string $code, string $filename = null): array;
}
