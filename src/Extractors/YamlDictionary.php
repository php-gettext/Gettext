<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Symfony\Component\Yaml\Parser;

/**
 * Class to get gettext strings from plain json.
 */
class YamlDictionary extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $yml = new Parser();

        if (($entries = $yml->parse($string))) {
            foreach ($entries as $original => $translation) {
                $translations->insert(null, $original)->setTranslation($translation);
            }
        }

        return $translations;
    }
}
