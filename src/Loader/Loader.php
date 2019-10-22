<?php
declare(strict_types = 1);

namespace Gettext\Loader;

use Gettext\Translations;
use Gettext\Translation;
use Exception;

/**
 * Base class with common funtions for all loaders
 */
abstract class Loader implements LoaderInterface
{
    protected $translations;

    public function __construct(Translations $translations = null)
    {
        $this->setTranslations($translations ?: new Translations());
    }

    public function setTranslations(Translations $translations): void
    {
        $this->translations = $translations;
    }

    public function getTranslations(): Translations
    {
        return $this->translations;
    }

    public function loadFile(string $filename): void
    {
        $string = static::readFile($filename);

        $this->loadString($string);
    }

    public function loadString(string $string): void
    {
    }

    protected function createTranslation(?string $context, string $original, string $plural = null): ?Translation
    {
        $translation = Translation::create($context, $original);

        if (isset($plural)) {
            $translation->setPlural($plural);
        }

        return $translation;
    }

    /**
     * Reads and returns the content of a file.
     */
    protected static function readFile(string $file): string
    {
        $length = filesize($file);

        if (!($fd = fopen($file, 'rb'))) {
            throw new Exception("Cannot read the file '$file', probably permissions");
        }

        $content = $length ? fread($fd, $length) : '';
        fclose($fd);

        return $content;
    }
}
