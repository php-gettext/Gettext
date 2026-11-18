<?php
declare(strict_types = 1);

namespace Gettext\Generator;

use Gettext\Translations;

final class PoGenerator extends Generator
{
    public function generateString(Translations $translations): string
    {
        $pluralForm = $translations->getHeaders()->getPluralForm();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $lines = [];

        //Description and flags
        if ($translations->getDescription()) {
            $description = explode("\n", $translations->getDescription());

            foreach ($description as $line) {
                $lines[] = sprintf('# %s', $line);
            }

            $lines[] = '#';
        }

        if (count($translations->getFlags())) {
            $lines[] = sprintf('#, %s', implode(',', $translations->getFlags()->toArray()));
        }

        //Headers
        $lines[] = 'msgid ""';
        $lines[] = 'msgstr ""';

        foreach ($translations->getHeaders() as $name => $value) {
            $lines[] = sprintf('"%s: %s\\n"', $name, $value);
        }

        $lines[] = '';

        //Translations
        foreach ($translations as $translation) {
            foreach ($translation->getComments() as $comment) {
                $lines[] = sprintf('# %s', $comment);
            }

            foreach ($translation->getExtractedComments() as $comment) {
                $lines[] = sprintf('#. %s', $comment);
            }

            foreach ($translation->getReferences() as $filename => $lineNumbers) {
                if (empty($lineNumbers)) {
                    $lines[] = sprintf('#: %s', $filename);
                    continue;
                }

                foreach ($lineNumbers as $number) {
                    $lines[] = sprintf('#: %s:%d', $filename, $number);
                }
            }

            if (count($translation->getFlags())) {
                $lines[] = sprintf('#, %s', implode(',', $translation->getFlags()->toArray()));
            }

            $prefix = $translation->isDisabled() ? '#~ ' : '';

            $previousContext = $translation->getPreviousContext();
            if (self::hasValue($previousContext)) {
                $lines[] = sprintf('%s#| msgctxt %s', $prefix, self::encode($previousContext));
            }

            $previousOriginal = $translation->getPreviousOriginal();
            if (self::hasValue($previousOriginal)) {
                $lines[] = sprintf('%s#| msgid %s', $prefix, self::encode($previousOriginal));
            }

            $previousPlural = $translation->getPreviousPlural();
            if (self::hasValue($previousPlural)) {
                $lines[] = sprintf('%s#| msgid_plural %s', $prefix, self::encode($previousPlural));
            }

            $context = $translation->getContext();
            if (self::hasValue($context)) {
                $lines[] = sprintf('%smsgctxt %s', $prefix, self::encode($context));
            }

            self::appendLines($lines, $prefix, 'msgid', $translation->getOriginal());

            $plural = $translation->getPlural();
            if (self::hasValue($plural)) {
                self::appendLines($lines, $prefix, 'msgid_plural', $plural);
                self::appendLines($lines, $prefix, 'msgstr[0]', $translation->getTranslation() ?? '');

                foreach ($translation->getPluralTranslations($pluralSize) as $k => $v) {
                    self::appendLines($lines, $prefix, sprintf('msgstr[%d]', $k + 1), $v);
                }
            } else {
                self::appendLines($lines, $prefix, 'msgstr', $translation->getTranslation() ?? '');
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * Checks whether a string is set and not empty. Unlike a truthiness check, "0" is a valid value.
     *
     * @phpstan-assert-if-true string $value
     */
    private static function hasValue(?string $value): bool
    {
        return $value !== null && $value !== '';
    }

    /**
     * Add one or more lines depending whether the string is multiline or not.
     */
    private static function appendLines(array &$lines, string $prefix, string $name, string $value): void
    {
        $newLines = explode("\n", $value);
        $total = count($newLines);

        if ($total === 1) {
            $lines[] = sprintf('%s%s %s', $prefix, $name, self::encode($newLines[0]));

            return;
        }

        $lines[] = sprintf('%s%s ""', $prefix, $name);

        $last = $total - 1;
        foreach ($newLines as $k => $line) {
            if ($k < $last) {
                $line .= "\n";
            }

            $lines[] = self::encode($line);
        }
    }

    /**
     * Convert a string to its PO representation.
     */
    public static function encode(string $value): string
    {
        return '"'.strtr(
            $value,
            [
                "\x00" => '',
                '\\' => '\\\\',
                "\t" => '\t',
                "\r" => '\r',
                "\n" => '\n',
                '"' => '\\"',
            ]
        ).'"';
    }
}
