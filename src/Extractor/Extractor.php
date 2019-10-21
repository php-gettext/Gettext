<?php
declare(strict_types = 1);

namespace Gettext\Extractor;

use Gettext\Translations;

abstract class Extractor implements ExtractorInterface
{
    protected $options;
    protected $translations;

    public function setTranslations(Translations ...$allTranslations): self
    {
        foreach ($allTranslations as $translations) {
            $domain = $translations->getDomain();
            $this->translations[$domain] = $translations;
        }
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options + $this->options;
        
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function extractFromFile(string $filename): self
    {
        $string = static::readFile($filename);

        return $this->extractfromString($string, $filename);
    }

    public function extractfromString(string $string, string $filename = null): self
    {
        return $this;
    }

    protected function saveTranslation(?string $domain, ?string $context, string $original, string $plural = null): ?Translation
    {
        if (!isset($this->translations[$domain])) {
            return null;
        }

        $translation = Translation::create($context, $original);

        $this->translations[$domain]->add($translation);

        if (isset($plural)) {
            $translation->setPlural($plural);
        }

        return $translation;
    }
}
