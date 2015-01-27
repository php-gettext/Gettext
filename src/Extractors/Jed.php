<?php
namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from json files
 */
class Jed extends PhpArray implements ExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    protected static function fromStringDo($string, Translations $translations, $file)
    {
        $content = json_decode($string);

        PhpArray::handleArray($content, $translations);
    }
}
