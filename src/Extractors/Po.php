<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Translation;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class Po extends Extractor implements ExtractorInterface
{
    /**
     * Parses a .po file and append the translations found in the Translations instance.
     *
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $lines = explode("\n", $string);
        $i = 0;

        $translation = new Translation('', '');

        for ($n = count($lines); $i < $n; ++$i) {
            $line = trim($lines[$i]);

            $line = self::fixMultiLines($line, $lines, $i);

            if ($line === '') {
                if ($translation->is('', '')) {
                    self::parseHeaders($translation->getTranslation(), $translations);
                } elseif ($translation->hasOriginal()) {
                    $translations[] = $translation;
                }

                $translation = new Translation('', '');
                continue;
            }

            $splitLine = preg_split('/\s+/', $line, 2);
            $key = $splitLine[0];
            $data = isset($splitLine[1]) ? $splitLine[1] : '';

            switch ($key) {
                case '#':
                    $translation->addComment($data);
                    $append = null;
                    break;

                case '#.':
                    $translation->addExtractedComment($data);
                    $append = null;
                    break;

                case '#,':
                    foreach (array_map('trim', explode(',', trim($data))) as $value) {
                        $translation->addFlag($value);
                    }
                    $append = null;
                    break;

                case '#:':
                    foreach (preg_split('/\s+/', trim($data)) as $value) {
                        if (preg_match('/^(.+)(:(\d*))?$/U', $value, $matches)) {
                            $translation->addReference($matches[1], isset($matches[3]) ? $matches[3] : null);
                        }
                    }
                    $append = null;
                    break;

                case 'msgctxt':
                    $translation = $translation->getClone(self::convertString($data));
                    $append = 'Context';
                    break;

                case 'msgid':
                    $translation = $translation->getClone(null, self::convertString($data));
                    $append = 'Original';
                    break;

                case 'msgid_plural':
                    $translation->setPlural(self::convertString($data));
                    $append = 'Plural';
                    break;

                case 'msgstr':
                case 'msgstr[0]':
                    $translation->setTranslation(self::convertString($data));
                    $append = 'Translation';
                    break;

                case 'msgstr[1]':
                    $translation->setPluralTranslation(self::convertString($data), 0);
                    $append = 'PluralTranslation';
                    break;

                default:
                    if (strpos($key, 'msgstr[') === 0) {
                        $translation->setPluralTranslation(self::convertString($data), intval(substr($key, 7, -1)) - 1);
                        $append = 'PluralTranslation';
                        break;
                    }

                    if (isset($append)) {
                        if ($append === 'Context') {
                            $translation = $translation->getClone($translation->getContext()."\n".self::convertString($data));
                            break;
                        }

                        if ($append === 'Original') {
                            $translation = $translation->getClone(null, $translation->getOriginal()."\n".self::convertString($data));
                            break;
                        }

                        if ($append === 'PluralTranslation') {
                            $key = count($translation->getPluralTranslation()) - 1;
                            $translation->setPluralTranslation($translation->getPluralTranslation($key)."\n".self::convertString($data), $key);
                            break;
                        }

                        $getMethod = 'get'.$append;
                        $setMethod = 'set'.$append;
                        $translation->$setMethod($translation->$getMethod()."\n".self::convertString($data));
                    }
                    break;
            }
        }

        if ($translation->hasOriginal() && !in_array($translation, iterator_to_array($translations))) {
            $translations[] = $translation;
        }

        return $translations;
    }

    /**
     * Checks if it is a header definition line. Useful for distguishing between header definitions
     * and possible continuations of a header entry.
     *
     * @param string $line Line to parse
     *
     * @return bool
     */
    private static function isHeaderDefinition($line)
    {
        return (bool) preg_match('/^[\w-]+:/', $line);
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
        $currentHeader = null;

        foreach ($headers as $line) {
            $line = self::convertString($line);

            if (self::isHeaderDefinition($line)) {
                $header = array_map('trim', explode(':', $line, 2));
                $currentHeader = $header[0];
                $translations->setHeader($currentHeader, $header[1]);
            } else {
                $entry = $translations->getHeader($currentHeader);
                $translations->setHeader($currentHeader, $entry.$line);
            }
        }
    }

    /**
     * Gets one string from multiline strings.
     *
     * @param string $line
     * @param array  $lines
     * @param int    &$i
     *
     * @return string
     */
    private static function fixMultiLines($line, array $lines, &$i)
    {
        for ($j = $i, $t = count($lines); $j < $t; ++$j) {
            if (substr($line, -1, 1) == '"'
                && isset($lines[$j + 1])
                && substr(trim($lines[$j + 1]), 0, 1) == '"'
            ) {
                $line = substr($line, 0, -1).substr(trim($lines[$j + 1]), 1);
            } else {
                $i = $j;
                break;
            }
        }

        return $line;
    }

    /**
     * Convert a string from its PO representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function convertString($value)
    {
        if (!$value) {
            return '';
        }

        if ($value[0] === '"') {
            $value = substr($value, 1, -1);
        }

        return strtr(
            $value,
            array(
                '\\\\' => '\\',
                '\\a' => "\x07",
                '\\b' => "\x08",
                '\\t' => "\t",
                '\\n' => "\n",
                '\\v' => "\x0b",
                '\\f' => "\x0c",
                '\\r' => "\r",
                '\\"' => '"',
            )
        );
    }
}
