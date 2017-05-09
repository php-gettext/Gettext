<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\HeadersExtractorTrait;

/**
 * Class to get gettext strings from csv.
 */
class Csv extends Extractor implements ExtractorInterface
{
    use HeadersExtractorTrait;

    public static $options = [
        'delimiter' => ",",
        'enclosure' => '"',
        'escape_char' => "\\"
    ];

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $options += static::$options;
        $handle = fopen('php://memory', 'w');

        fputs($handle, $string);
        rewind($handle);

        while ($row = self::getcsv($handle, $options)) {
            $context = array_shift($row);
            $original = array_shift($row);

            if ($context === '' && $original === '') {
                self::extractHeaders(array_shift($row), $translations);
                continue;
            }

            $translation = $translations->insert($context, $original);

            if (!empty($row)) {
                $translation->setTranslation(array_shift($row));
                $translation->setPluralTranslations($row);
            }
        }

        fclose($handle);
    }

    /**
     * @param resource $handle
     * @param array $options
     *
     * @return array
     */
    private static function getcsv($handle, $options)
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) { // >= 5.3
            return fgetcsv($handle, null, $options['delimiter'], $options['enclosure'], $options['escape_char']);
        }

        return fgetcsv($handle, null, $options['delimiter'], $options['enclosure']);
    }
}
