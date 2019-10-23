<?php
declare(strict_types = 1);

namespace Gettext\Generator;

use Gettext\Translations;
use Gettext\Translation;
use Gettext\Headers;

final class ArrayGenerator extends Generator
{
    private $includeHeaders = true;

    public function includeHeaders(bool $includeHeaders = true): void
    {
        $this->includeHeaders = $includeHeaders;
    }

    public function generateString(Translations $translations): string
    {
        $array = $this->generateArray($translations);

        return sprintf('<?php return %s;', var_export($array, true));
    }

    public function generateArray(Translations $translations): array
    {
        $pluralForm = $translations->getHeaders()->getPluralForm();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = [];

        if ($this->includeHeaders) {
            $headers = [];

            foreach ($translations->getHeaders() as $name => $value) {
                $headers[] = sprintf("%s: %s", $name, $value);
            }

            $messages[''] = ['' => implode("\n", $headers)];
        }

        foreach ($translations as $translation) {
            if ($translation->isDisabled()) {
                continue;
            }

            $context = $translation->getContext() ?: '';
            $original = $translation->getOriginal();

            if (!isset($messages[$context])) {
                $messages[$context] = [];
            }

            if (self::hasPluralTranslations($translation)) {
                $messages[$context][$original] = $translation->getPluralTranslations($pluralSize);
                array_unshift($messages[$context][$original], $translation->getTranslation());
            } else {
                $messages[$context][$original] = $translation->getTranslation();
            }
        }

        return [
            'domain' => $translations->getDomain(),
            'plural-forms' => $translations->getHeaders()->get(Headers::HEADER_PLURAL),
            'messages' => $messages,
        ];
    }

    private static function hasPluralTranslations(Translation $translation): bool
    {
        return implode('', $translation->getPluralTranslations()) !== '';
    }
}
