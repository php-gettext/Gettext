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

        $handle = fopen('php://memory', 'w');

        //map to a simple csv dictionary (no plurals)
        foreach ($values as $original => $translated) {
            if (!isset($translated[1])) {
                $translated[1] = '';
            }
            fputcsv($handle, array($original, $translated[1]));
        }

        rewind($handle);
        $csv = stream_get_contents($handle);

        fclose($handle);

        return $csv;
    }
}
