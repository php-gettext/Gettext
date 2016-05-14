<?php

namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from csv.
 */
class Csv extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $handle = fopen('php://memory', 'w');

        fputs($handle, $string);
        rewind($handle);

        $entries = [];

        while ($row = fgetcsv($handle)) {
            $translation = $translations->insert(array_shift($row), array_shift($row));

            if (!empty($row)) {
                $translation->setTranslation(array_shift($row));
                $translation->setPluralTranslations($row);
            }
        }

        fclose($handle);

        return $translations;
    }
}
