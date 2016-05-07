<?php

namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from plain json.
 */
class JsonDictionary extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        if (($entries = json_decode($string, true))) {
            foreach ($entries as $original => $translation) {
                $translations->insert(null, $original)->setTranslation($translation);
            }
        }

        return $translations;
    }
}
