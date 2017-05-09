<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\HeadersGeneratorTrait;

/**
 * Class to export translations to csv.
 */
class Csv extends Generator implements GeneratorInterface
{
    use HeadersGeneratorTrait;

    public static $options = [
        'includeHeaders' => false,
        'delimiter' => ",",
        'enclosure' => '"',
        'escape_char' => "\\"
    ];

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;
        $handle = fopen('php://memory', 'w');

        if ($options['includeHeaders']) {
            self::fputcsv($handle, ['', '', self::generateHeaders($translations)], $options);
        }

        foreach ($translations as $translation) {
            $line = [$translation->getContext(), $translation->getOriginal(), $translation->getTranslation()];

            if ($translation->hasPluralTranslations(true)) {
                $line = array_merge($line, $translation->getPluralTranslations());
            }

            self::fputcsv($handle, $line, $options);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * @param resource $handle
     * @param array $fields
     * @param array $options
     *
     * @return bool|int
     */
    private static function fputcsv($handle, $fields, $options)
    {
        if (version_compare(PHP_VERSION, '5.5.4') >= 0) { // >= 5.5.4
            return fputcsv($handle, $fields, $options['delimiter'], $options['enclosure'], $options['escape_char']);
        }

        return fputcsv($handle, $fields, $options['delimiter'], $options['enclosure']);
    }
}
