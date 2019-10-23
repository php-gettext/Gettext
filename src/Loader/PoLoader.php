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
        $line = current($lines);
        $translation = $this->createTranslation(null, '');

        while ($line !== false) {
            $line = trim($line);
            $nextLine = next($lines);
            
            //Multiline
            while (
                substr($line, -1, 1) === '"'
                && $nextLine !== false
                && substr(trim($nextLine), 0, 1) === '"'
            ) {
                $line = substr($line, 0, -1).substr(trim($nextLine), 1);
                $nextLine = next($lines);
            }

            //End of translation
            if ($line === '') {
                if (!self::isEmpty($translation)) {
                    $translations->add($translation);
                }

                $translation = $this->createTranslation(null, '');
                $line = $nextLine;
                continue;
            }

            $splitLine = preg_split('/\s+/', $line, 2);
            $key = $splitLine[0];
            $data = $splitLine[1] ?? '';

            if ($key === '#~') {
                $translation->setDisabled(true);

                $splitLine = preg_split('/\s+/', $data, 2);
                $key = $splitLine[0];
                $data = $splitLine[1] ?? '';
            }

            if ($data === '') {
                $line = $nextLine;
                continue;
            }

            switch ($key) {
                case '#':
                    $translation->getComments()->add($data);
                    break;

                case '#.':
                    $translation->getExtractedComments()->add($data);
                    break;

                case '#,':
                    foreach (array_map('trim', explode(',', trim($data))) as $value) {
                        $translation->getFlags()->add($value);
                    }
                    break;

                case '#:':
                    foreach (preg_split('/\s+/', trim($data)) as $value) {
                        if (preg_match('/^(.+)(:(\d*))?$/U', $value, $matches)) {
                            $translation->getReferences()->add($matches[1], $matches[3] ?? null);
                        }
                    }
                    break;

                case 'msgctxt':
                    $translation = $translation->withContext(static::decode($data));
                    break;

                case 'msgid':
                    $translation = $translation->withOriginal(static::decode($data));
                    break;

                case 'msgid_plural':
                    $translation->setPlural(static::decode($data));
                    break;

                case 'msgstr':
                case 'msgstr[0]':
                    $translation->translate(static::decode($data));
                    break;

                case 'msgstr[1]':
                    $translation->translatePlural(static::decode($data));
                    break;

                default:
                    if (strpos($key, 'msgstr[') === 0) {
                        $p = $translation->getPluralTranslations();
                        $p[] = static::decode($data);

                        $translation->translatePlural(...$p);
                        break;
                    }
                    break;
            }

            $line = $nextLine;
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
