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
        $handle = fopen('php://memory', 'w');

        foreach ($translations as $translation) {
            fputcsv($handle, [$translation->getOriginal(), $translation->getTranslation()]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
