<?php

namespace Gettext\Generators;

use Gettext\Translations;

class CsvDictionary extends Generator implements GeneratorInterface
{
    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations)
    {
        $array = PhpArray::toArray($translations);

        //for a simple json translation dictionary, one domain is supported
        $values = current($array);

        // remove meta / header data
        if (array_key_exists('', $values)) {
            unset($values['']);
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'gettext_');
        $handle = fopen($tmpFile, 'w');

        //map to a simple csv dictionary (no plurals)
        foreach ($values as $original => $translated) {
            if (!isset($translated[1])) {
                $translated[1] = '';
            }
            fputcsv($handle, [$original, $translated[1]]);
        }

        fclose($handle);
        $csv = file_get_contents($tmpFile);
        unlink($tmpFile);

        return $csv;
    }
}
