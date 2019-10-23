<?php
declare(strict_types = 1);

namespace Gettext\Loader;

use Gettext\Translations;
use Gettext\Translation;
use Gettext\Headers;
use BadMethodCallException;

/**
 * Class to load a array file
 */
final class ArrayLoader extends Loader
{
    use HeadersLoaderTrait;

    public function loadFile(string $filename, Translations $translations = null): Translations
    {
        $array = self::includeSafe($filename);

        return $this->loadArray($array, $translations);
    }

    public function loadString(string $string, Translations $translations = null): Translations
    {
        throw new BadMethodCallException('Arrays cannot be loaded from string. Use ArrayLoader::loadFile() instead');
    }

    private static function includeSafe($filename): array
    {
        return include($filename);
    }

    public function loadArray(array $array, Translations $translations = null): Translations
    {
        if (!$translations) {
            $translations = $this->createTranslations();
        }

        $messages = $array['messages'] ?? [];

        foreach ($messages as $context => $contextTranslations) {
            if ($context === '') {
                $context = null;
            }

            foreach ($contextTranslations as $original => $value) {
                //Headers
                if ($context === null && $original === '') {
                    $string = is_array($value) ? array_shift($value) : $value;
                    $headers = $translations->getHeaders();

                    foreach (self::parseHeaders($string) as $name => $value) {
                        $headers->set($name, $value);
                    }
                    continue;
                }

                $translation = $this->createTranslation($context, $original);
                $translations->add($translation);

                if (is_array($value)) {
                    $translation->translate(array_shift($value));
                    $translation->translatePlural(...$value);
                } else {
                    $translation->translate($value);
                }
            }
        }

        if (!empty($array['domain'])) {
            $translations->setDomain($array['domain']);
        }

        if (!empty($array['plural-forms'])) {
            $translations->getHeaders()->set(Headers::HEADER_PLURAL, $array['plural-forms']);
        }

        return $translations;
    }
}
