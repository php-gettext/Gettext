<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class to get gettext strings from plain json.
 */
class Yaml extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $entries = (array) YamlParser::parse($string);
        
        foreach ($entries as $context => $contextTranslations) {
            foreach ($contextTranslations as $original => $value) {
                $translation = $translations->insert($context, $original);

                if (is_array($value)) {
                    $translation->setTranslation(array_shift($value));
                    $translation->setPluralTranslations($value);
                } else {
                    $translation->setTranslation($value);
                }
            }
        }

        return $translations;
    }
}
