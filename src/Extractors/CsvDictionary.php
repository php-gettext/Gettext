<?php

namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from plain json.
 */
class CsvDictionary extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $handle = fopen('php://memory', 'w');

        fputs($handle, $string);
        rewind($handle);

        $entries = array();
        while ($row = fgetcsv($handle)) {
            $entries[$row[0]] = $row[1];
        }

        fclose($handle);

        if ($entries) {
            foreach ($entries as $original => $translation) {
                $translations->insert(null, $original)->setTranslation($translation);
            }
        }

        return $translations;
    }
}
