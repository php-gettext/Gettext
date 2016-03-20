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

        $tmpFile = tempnam(sys_get_temp_dir(), 'gettext_');
        file_put_contents($tmpFile, $string);
        $handle = fopen($tmpFile, 'r');

        $entries = [];
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
