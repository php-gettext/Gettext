<?php

namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from csv.
 */
class CsvDictionary extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $handle = fopen('php://memory', 'w');

        fputs($handle, $string);
        rewind($handle);

        while ($row = fgetcsv($handle)) {
            $translations->insert(null, $row[0])->setTranslation(isset($row[1]) ? $row[1] : '');
        }

        fclose($handle);

        return $translations;
    }
}
