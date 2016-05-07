<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Symfony\Component\Yaml\Yaml;

/**
 * Class to get gettext strings from plain json.
 */
class YamlDictionary extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        if (($entries = Yaml::parse($string))) {
            foreach ($entries as $original => $translation) {
                $translations->insert(null, $original)->setTranslation($translation);
            }
        }

        return $translations;
    }
}
