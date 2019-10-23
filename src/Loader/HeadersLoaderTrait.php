<?php

namespace Gettext\Loader;

use Gettext\Translations;

/**
 * Trait to provide the functionality of parsing headers.
 */
trait HeadersLoaderTrait
{
    private static function parseHeaders(string $string): array
    {
        $headers = [];
        $lines = explode("\n", $string);
        $name = null;

        foreach ($lines as $line) {
            $line = self::decode($line);

            if ($line === '') {
                continue;
            }

            if (self::isHeaderDefinition($line)) {
                $pieces = array_map('trim', explode(':', $line, 2));
                list($name, $value) = $pieces;

                $headers[$name] = $value;
                continue;
            }

            $value = $headers[$name] ?? '';
            $headers[$name] = $value.$line;
        }

        return $headers;
    }

    /**
     * Checks if it is a header definition line. Useful for distinguishing between header definitions
     * and possible continuations of a header entry.
     */
    private static function isHeaderDefinition(string $line): bool
    {
        return (bool) preg_match('/^[\w-]+:/', $line);
    }

    /**
     * Normalize a string.
     */
    public static function decode(string $value): string
    {
        return trim($value);
    }
}
