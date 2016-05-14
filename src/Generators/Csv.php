<?php

namespace Gettext\Generators;

use Gettext\Translations;

/**
 * Class to export translations to csv.
 */
class Csv extends Generator implements GeneratorInterface
{
    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $handle = fopen('php://memory', 'w');

        foreach ($translations as $translation) {
            $line = [$translation->getContext(), $translation->getOriginal(), $translation->getTranslation()];

            if ($translation->hasPluralTranslations()) {
                $line = array_merge($line, $translation->getPluralTranslations());
            }

            fputcsv($handle, $line);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
