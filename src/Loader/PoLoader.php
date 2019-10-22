<?php

namespace Gettext\Loader;

use Gettext\Translations;
use Gettext\Translation;

/**
 * Class to load a PO file
 */
final class PoLoader extends Loader
{
    use HeadersLoaderTrait;

    public function loadString(string $string, Translations $translations = null): Translations
    {
        $translations = parent::loadString($string, $translations);

        $lines = explode("\n", $string);
        $i = 0;
        $append = null;

        $translation = $this->createTranslation(null, '');

        for ($n = count($lines); $i < $n; ++$i) {
            $line = trim($lines[$i]);
            $line = self::fixMultiLines($line, $lines, $i);

            if ($line === '') {
                if (!self::isEmpty($translation)) {
                    $translations->add($translation);
                }

                $translation = $this->createTranslation(null, '');
                continue;
            }

            $splitLine = preg_split('/\s+/', $line, 2);
            $key = $splitLine[0];
            $data = isset($splitLine[1]) ? $splitLine[1] : '';

            if ($key === '#~') {
                $translation->setDisabled(true);

                $splitLine = preg_split('/\s+/', $data, 2);
                $key = $splitLine[0];
                $data = isset($splitLine[1]) ? $splitLine[1] : '';
            }

            if ($data === '') {
                continue;
            }

            switch ($key) {
                case '#':
                    $translation->getComments()->add($data);
                    $append = null;
                    break;

                case '#.':
                    $translation->getExtractedComments()->add($data);
                    $append = null;
                    break;

                case '#,':
                    foreach (array_map('trim', explode(',', trim($data))) as $value) {
                        $translation->getFlags()->add($value);
                    }
                    $append = null;
                    break;

                case '#:':
                    foreach (preg_split('/\s+/', trim($data)) as $value) {
                        if (preg_match('/^(.+)(:(\d*))?$/U', $value, $matches)) {
                            $translation->getReferences()->add($matches[1], isset($matches[3]) ? $matches[3] : null);
                        }
                    }
                    $append = null;
                    break;

                case 'msgctxt':
                    $translation = $translation->withContext(static::decode($data));
                    $append = 'Context';
                    break;

                case 'msgid':
                    $translation = $translation->withOriginal(static::decode($data));
                    $append = 'Original';
                    break;

                case 'msgid_plural':
                    $translation->setPlural(static::decode($data));
                    $append = 'Plural';
                    break;

                case 'msgstr':
                case 'msgstr[0]':
                    $translation->translate(static::decode($data));
                    $append = 'Translation';
                    break;

                case 'msgstr[1]':
                    $translation->translatePlural(static::decode($data));
                    $append = 'PluralTranslation';
                    break;

                default:
                    if (strpos($key, 'msgstr[') === 0) {
                        $p = $translation->getPluralTranslations();
                        $p[] = static::decode($data);

                        $translation->translatePlural(...$p);
                        $append = 'PluralTranslation';
                        break;
                    }

                    if (isset($append)) {
                        if ($append === 'Context') {
                            $context = $translation->getContext()."\n".static::decode($data);
                            $translation = $translation->withContext($context);
                            break;
                        }

                        if ($append === 'Original') {
                            $original = $translation->getOriginal()."\n".static::decode($data);
                            $translation = $translation->withOriginal($original);
                            break;
                        }

                        if ($append === 'Plural') {
                            $plural = $translation->getPlural()."\n".static::decode($data);
                            $translation->setPlural($plural);
                            break;
                        }

                        if ($append === 'Translation') {
                            $text = $translation->getTranslation()."\n".static::decode($data);
                            $translation->translate($text);
                            break;
                        }

                        if ($append === 'PluralTranslation') {
                            $p = $translation->getPluralTranslations();
                            $p[] = array_pop($p)."\n".static::decode($data);
                            $translation->translatePlural(...$p);
                            break;
                        }
                    }
                    break;
            }
        }

        if (!self::isEmpty($translation)) {
            $translations->add($translation);
        }

        //Headers
        $translation = $translations->find(null, '');

        if (!$translation) {
            return $translations;
        }

        $translations->remove($translation);
        $headers = $translations->getHeaders();

        foreach (static::parseHeaders($translation->getTranslation()) as $name => $value) {
            $headers->set($name, $value);
        }

        return $translations;
    }

    /**
     * Gets one string from multiline strings.
     */
    private static function fixMultiLines(string $line, array $lines, int &$i): string
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
     */
    public static function decode(string $value): string
    {
        if (!$value) {
            return '';
        }

        if ($value[0] === '"') {
            $value = substr($value, 1, -1);
        }

        return strtr(
            $value,
            [
                '\\\\' => '\\',
                '\\a' => "\x07",
                '\\b' => "\x08",
                '\\t' => "\t",
                '\\n' => "\n",
                '\\v' => "\x0b",
                '\\f' => "\x0c",
                '\\r' => "\r",
                '\\"' => '"',
            ]
        );
    }

    private static function isEmpty(Translation $translation): bool
    {
        if (!empty($translation->getOriginal())) {
            return false;
        }

        if (!empty($translation->getTranslation())) {
            return false;
        }

        return true;
    }
}