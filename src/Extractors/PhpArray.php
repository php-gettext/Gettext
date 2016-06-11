<?php

namespace Gettext\Extractors;

use BadMethodCallException;
use Gettext\Translations;
use Gettext\Utils\MultidimensionalArrayTrait;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpArray extends Extractor implements ExtractorInterface
{
    use MultidimensionalArrayTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            static::extract(include($file), $translations);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        throw new BadMethodCallException('PhpArray::fromString() cannot be called. Use PhpArray::fromFile()');
    }

    /**
     * Handle an array of translations and append to the Translations instance.
     *
     * @param array        $content
     * @param Translations $translations
     */
    public static function extract(array $content, Translations $translations)
    {
        self::fromArray($content['messages'], $translations);

        if (!empty($content['domain'])) {
            $translations->setDomain($content['domain']);
        }

        if (!empty($content['plural-forms'])) {
            $translations->setHeader(Translations::HEADER_PLURAL, $content['plural-forms']);
        }
    }
}
