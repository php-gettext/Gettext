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
        'escape' => "\\"
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

        while ($row = fgetcsv($handle, null, $options['delimiter'], $options['enclosure'], $options['escape'])) {
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
}
