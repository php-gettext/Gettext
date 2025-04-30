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
    ->withTypeCoverageLevel(1)
    ->withDeadCodeLevel(2)
    ->withCodeQualityLevel(10)
    ->withCodingStyleLevel(0)
;
