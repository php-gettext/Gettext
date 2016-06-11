<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\HeadersExtractorTrait;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Class to get gettext strings from plain json.
 */
class Yaml extends Extractor implements ExtractorInterface
{
    use HeadersExtractorTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $entries = (array) YamlParser::parse($string);

        foreach ($entries as $context => $contextTranslations) {
            foreach ($contextTranslations as $original => $value) {
                if ($context === '' && $original === '') {
                    self::extractHeaders(array_shift($value), $translations);
                    continue;
                }

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
