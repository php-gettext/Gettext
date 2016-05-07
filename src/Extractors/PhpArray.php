<?php

namespace Gettext\Extractors;

use BadMethodCallException;
use Gettext\Translations;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpArray extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            static::extract(include($file), $translations);
        }

        return $translations;
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
        foreach ($content['messages'] as $context => $messages) {
            foreach ($messages as $original => $translation) {
                if ($original === '' && $context === '') {
                    self::parseHeaders(array_shift($translation), $translations);
                    continue;
                }

                $translations->insert($context, $original)
                    ->setTranslation(array_shift($translation))
                    ->setPluralTranslations($translation);
            }
        }

        if (!empty($content['domain'])) {
            $translations->setDomain($content['domain']);
        }

        if (!empty($content['plural-forms'])) {
            $translations->setHeader(Translations::HEADER_PLURAL, $content['plural-forms']);
        }
    }

    /**
     * Parse the po headers.
     *
     * @param string       $headers
     * @param Translations $translations
     */
    private static function parseHeaders($headers, Translations $translations)
    {
        $headers = explode("\n", $headers);

        foreach ($headers as $line) {
            if (strpos($line, ':') !== false) {
                $header = array_map('trim', explode(':', $line, 2));
                $translations->setHeader($header[0], $header[1]);
            }
        }
    }
}
