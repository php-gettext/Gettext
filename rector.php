<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(typeDeclarations: true)
    ->withDeadCodeLevel(2)
    ->withCodeQualityLevel(10)
    ->withCodingStyleLevel(0)
;
