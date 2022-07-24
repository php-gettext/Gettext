<?php
declare(strict_types = 1);

namespace Gettext\Loader;

use Exception;
use Gettext\Translation;
use Gettext\Translations;

/**
 * Class to load a PO file following the same rules of the GNU gettext tools.
 */
final class StrictPoLoader extends Loader
{
    /** @var Translations */
    private $translations;
    /** @var Translation */
    private $translation;
    /** @var Translation|null */
    private $header;
    /** @var string */
    private $data;
    /** @var int */
    private $position;
    /** @var int|null */
    private $pluralCount;
    /** @var bool */
    private $inPreviousComment;
    /** @var bool */
    private $throwOnWarning;
    /** @var string[] */
    private $warnings = [];

    /**
     * Generates a Translations object from a .po based string
     */
    public function loadString(string $string, Translations $translations = null): Translations
    {
        return $this->loadStringExtended(...func_get_args());
    }

    /**
     * Generates a Translations object from a .po based string with extra options
     */
    public function loadStringExtended(
        string $string,
        Translations $translations = null,
        bool $throwOnWarning = false
    ): Translations {
        $this->data = $string;
        $this->position = 0;
        $this->translations = parent::loadString($string, $translations);
        $this->header = $this->translations->find(null, '');
        $this->pluralCount = $this->translations->getHeaders()->getPluralForm()[0] ?? null;
        $this->throwOnWarning = $throwOnWarning;
        $this->warnings = [];
        for ($this->newEntry(); $this->getChar() !== null; $this->newEntry()) {
            while ($this->readComment());
            if ($this->getChar() === null) {
                $this->addWarning("Comment ignored at the end of the string at byte {$this->position}");
            }
            $this->readContext();
            $this->readOriginal();
            if ($this->readPlural() && $this->readPluralTranslation(true)) {
                while ($this->readPluralTranslation());
            } else {
                $this->readTranslation();
            }
            $this->saveEntry();
        }
        if (!$this->header) {
            $this->addWarning("The loaded string has no header translation at byte {$this->position}");
        }

        return $this->translations;
    }

    /**
     * Retrieves the collected warnings
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Prepares to parse a new translation
     */
    private function newEntry(): void
    {
        $this->inPreviousComment = false;
        $this->translation = $this->createTranslation(null, '');
    }

    /**
     * Adds the current translation to the output list
     */
    private function saveEntry(): void
    {
        if ($this->translation->getOriginal() === '' && $this->translation->getContext() === null) {
            $this->processHeader();

            return;
        }
        if ($this->translations->getTranslations()[$this->translation->getId()] ?? null) {
            throw new Exception("Duplicated entry at byte {$this->position}");
        }
        if ($this->pluralCount !== null && $this->translation->getPlural() !== null
            && ($translationCount = count($this->translation->getPluralTranslations())) !== ($this->pluralCount - 1)) {
            $this->addWarning("The translation has {$translationCount} plural forms, "
                ."while the header expects {$this->pluralCount} at byte {$this->position}");
        }
        $this->translations->add($this->translation);
    }

    /**
     * Attempts to read the prolog of a disabled comment
     */
    private function readDisabledComment(): bool
    {
        return $this->translation->isDisabled() && $this->readString('#~');
    }

    /**
     * Attempts to read the prolog of a previous translation comment
     */
    private function readPreviousTranslationComment(): bool
    {
        return $this->inPreviousComment && $this->readString('#|');
    }

    /**
     * Attempts to read whitespace characters, also might skip complex comment prologs when needed
     */
    private function readWhitespace(): bool
    {
        $position = $this->position;
        while ((ctype_space($this->getChar() ?? '') && $this->nextChar())
            || $this->readDisabledComment()
            || $this->readPreviousTranslationComment());

        return $position !== $this->position;
    }

    /**
     * Attempts to read the exact informed string
     */
    private function readString(string $word): bool
    {
        return substr($this->data, $this->position, strlen($word)) === $word
            ? (bool) ($this->position += strlen($word))
            : false;
    }

    /**
     * Attempts to read the exact informed char
     */
    private function readChar(string $char): bool
    {
        return $this->getChar() === $char ? (bool) ++$this->position : false;
    }

    /**
     * Retrieves the current char and advances the internal pointer
     */
    private function nextChar(): ?string
    {
        $char = $this->getChar();
        if ($char !== null) {
            ++$this->position;
        }

        return $char;
    }

    /**
     * Retrieves the current char without advancing the internal pointer
     */
    private function getChar(): ?string
    {
        return $this->data[$this->position] ?? null;
    }

    /**
     * Attempts to read a numeric sequence
     */
    private function readNumber(): string
    {
        for ($data = ''; ctype_digit($this->getChar() ?? ''); $data .= $this->nextChar());

        return $data;
    }

    /**
     * Read sequential characters that match the given character set until the length range is satisfied
     */
    private function readCharset(string $charset, int $min, int $max, string $name): string
    {
        for ($data = ''; ($char = $this->getChar()) !== null
            && is_int(strpos($charset, $char))
            && --$max >= 0; $data .= $this->nextChar());
        if (strlen($data) < $min) {
            throw new Exception("Expected at least one occurrence of {$name} characters at byte {$this->position}");
        }

        return $data;
    }

    /**
     * Attempts to read a standard comment string which ends with a newline
     */
    private function readCommentString(): string
    {
        for ($data = ''; ($char = $this->getChar() ?? "\n") !== "\n" && $char !== "\r"; $data .= $this->nextChar());

        return $data;
    }

    /**
     * Attempts to read a quoted string and unescape characters prefixed by \
     */
    private function readQuotedString(): string
    {
        static
            $aliases = [
                '\\' => '\\', 'a' => "\x07", 'b' => "\x08", 'e' => "\e", 'f' => "\f",
                'n' => "\n", 'r' => "\r", 't' => "\t", 'v' => "\v", '"' => '"',
            ],
            $octalDigits = '01234567',
            $hexDigits = '0123456789abcdefABCDEF';
        for ($checkpoint = null, $data = '', $pieces = 0;; ++$pieces) {
            if (!$this->readChar('"')) {
                // The data is over (e.g. beginning of an identifier) or there's an error
                // Restore the checkpoint and let the next parser handle it
                if ($pieces) {
                    $this->position = $checkpoint;
                    break;
                }
                throw new Exception("Expected an opening quote at byte {$this->position}");
            }
            // Collects chars until the end of the sequence/file
            for (; ($char = $this->getChar() ?? '"') !== '"'; $data .= $char) {
                if ($char === "\n" || $char === "\r") {
                    throw new Exception("Newline character must be escaped at byte {$this->position}");
                }
                if ($this->nextChar() !== '\\') {
                    continue;
                }
                switch ($escaped = $this->nextChar()) {
                    case ($alias = $aliases[$escaped] ?? null) !== null ? $escaped : '--':
                        $char = $alias;
                        break;
                    case $octalDigit = is_int(strpos($octalDigits, $escaped)) ? $escaped : '--':
                        $value = $octalDigit . $this->readCharset($octalDigits, 0, 2, 'octal');
                        // GNU gettext fails with an octal above the signed char range
                        if (($decimal = octdec($value)) > 127) {
                            throw new Exception("Octal value out of range [0, 0177] at byte {$this->position}");
                        }
                        $char = chr($decimal);
                        break;
                    case 'U':
                    case 'u':
                        // The GNU gettext is supposed to follow the escaping sequences of C
                        // Curiously it doesn't support the unicode escape
                        $value = $this->readCharset($hexDigits, 1, $digits = $escaped === 'u' ? 4 : 8, 'hexadecimal');
                        $value = str_pad($value, $digits, '0', STR_PAD_LEFT);
                        $char = mb_convert_encoding(hex2bin($value), 'UTF-8', 'UTF-' . ($digits * 4));
                        break;
                    case 'x':
                        $value = $this->readCharset($hexDigits, 1, PHP_INT_MAX, 'hexadecimal');
                        // GNU reads all valid hexadecimal chars, but only uses the last pair
                        $char = hex2bin(str_pad(substr($value, -2), 2, '0', STR_PAD_LEFT));
                        break;
                    default:
                        throw new Exception("Invalid quoted character at byte {$this->position}");
                }
            }
            if (!$this->readChar('"')) {
                throw new Exception("Expected a closing quote at byte {$this->position}");
            }
            // Saves a checkpoint and attempts to read a new sequence
            $checkpoint = $this->position;
            $this->readWhitespace();
        }

        return $data;
    }

    /**
     * Attempts to read and interpret a comment
     */
    private function readComment(): bool
    {
        $this->readWhitespace();
        if (!$this->readChar('#')) {
            return false;
        }
        $type = '';
        if (is_int(strpos('~|,:.', $char = $this->getChar() ?? ''))) {
            $type = $char;
            ++$this->position;
        }
        // Only a single space might be optionally added
        $this->readChar(' ');
        switch ($type) {
            case '':
                $data = $this->readCommentString();
                $this->translation->getComments()->add($data);
                break;
            case '~':
                if ($this->translation->getPreviousOriginal() !== null) {
                    throw new Exception("Inconsistent use of #~ at byte {$this->position}");
                }
                $this->translation->disable();
                break;
            case '|':
                if ($this->translation->getPreviousOriginal() !== null) {
                    throw new Exception('Cannot redeclare the previous comment #|, '
                        . "ensure the definitions are in the right order at byte {$this->position}");
                }
                $this->inPreviousComment = true;
                $this->translation->setPreviousContext($this->readIdentifier('msgctxt'));
                $this->translation->setPreviousOriginal($this->readIdentifier('msgid', true));
                $this->translation->setPreviousPlural($this->readIdentifier('msgid_plural'));
                $this->inPreviousComment = false;
                break;
            case ',':
                $data = $this->readCommentString();
                foreach (array_map('trim', explode(',', trim($data))) as $value) {
                    $this->translation->getFlags()->add($value);
                }
                break;
            case ':':
                $data = $this->readCommentString();
                foreach (preg_split('/\s+/', trim($data)) as $value) {
                    if (preg_match('/^(.+)(:(\d*))?$/U', $value, $matches)) {
                        $line = isset($matches[3]) ? intval($matches[3]) : null;
                        $this->translation->getReferences()->add($matches[1], $line);
                    }
                }
                break;
            case '.':
                $data = $this->readCommentString();
                $this->translation->getExtractedComments()->add($data);
                break;
        }

        return true;
    }

    /**
     * Attempts to read an identifier
     */
    private function readIdentifier(string $identifier, bool $throwIfNotFound = false): ?string
    {
        $checkpoint = $this->position;
        $this->readWhitespace();
        if (!$this->readString($identifier)) {
            if ($throwIfNotFound) {
                throw new Exception("Expected $identifier at byte {$this->position}");
            }
            $this->position = $checkpoint;

            return null;
        }
        $this->readWhitespace();

        return $this->readQuotedString();
    }

    /**
     * Attempts to read the context
     */
    private function readContext(): bool
    {
        if (($data = $this->readIdentifier('msgctxt')) === null) {
            return false;
        }
        $this->translation = $this->translation->withContext($data);

        return true;
    }

    /**
     * Reads the original message
     */
    private function readOriginal(): void
    {
        $data = $this->readIdentifier('msgid', true);
        $this->checkNewLine($data, 'msgid');
        $this->translation = $this->translation->withOriginal($data);
    }

    /**
     * Attempts to read the plural message
     */
    private function readPlural(): bool
    {
        if (($data = $this->readIdentifier('msgid_plural')) === null) {
            return false;
        }
        $this->checkNewLine($data, 'msgid_plural');
        $this->translation->setPlural($data);

        return true;
    }

    /**
     * Reads the translation
     */
    private function readTranslation(): void
    {
        $this->readWhitespace();
        if (!$this->readString('msgstr')) {
            throw new Exception("Expected msgstr at byte {$this->position}");
        }
        $this->readWhitespace();
        $data = $this->readQuotedString();
        // The header might be surrounded by newlines
        if ($this->translation->getOriginal() !== '') {
            $this->checkNewLine($data, 'msgstr');
        }
        $this->translation->translate($data);
    }

    /**
     * Attempts to read the pluralized translation
     */
    private function readPluralTranslation(bool $throwIfNotFound = false): bool
    {
        $this->readWhitespace();
        if (!$this->readString('msgstr')) {
            if ($throwIfNotFound) {
                throw new Exception("Expected indexed msgstr at byte {$this->position}");
            }

            return false;
        }
        $this->readWhitespace();
        if (!$this->readChar('[')) {
            throw new Exception("Expected character \"[\" at byte {$this->position}");
        }
        $this->readWhitespace();
        if (!strlen($index = $this->readNumber())) {
            throw new Exception("Expected msgstr index at byte {$this->position}");
        }
        $this->readWhitespace();
        if (!$this->readChar(']')) {
            throw new Exception("Expected character \"]\" at byte {$this->position}");
        }
        $translations = $this->translation->getPluralTranslations();
        if (($translation = $this->translation->getTranslation()) !== null) {
            array_unshift($translations, $translation);
        }
        if (count($translations) !== (int) $index) {
            throw new Exception("The msgstr has an invalid index at byte {$this->position}");
        }
        $this->readWhitespace();
        $data = $this->readQuotedString();
        $translations[] = $data;
        $this->checkNewLine($data, 'msgstr');
        $this->translation->translate(array_shift($translations));
        $this->translation->translatePlural(...$translations);

        return true;
    }

    /**
     * Setup the current translation as the header translation
     */
    private function processHeader(): void
    {
        $this->header = $header = $this->translation;
        $description = $header->getComments()->toArray();
        if (!empty($description)) {
            $this->translations->setDescription(implode("\n", $description));
        }

        $flags = $header->getFlags()->toArray();
        if (!empty($flags)) {
            $this->translations->getFlags()->add(...$flags);
        }

        $headers = $this->translations->getHeaders();
        $newHeaders = self::readHeaders($header->getTranslation() ?? '');
        foreach ($newHeaders as $name => $value) {
            $headers->set($name, $value);
        }
        $this->pluralCount = $headers->getPluralForm()[0] ?? null;

        foreach (['Language', 'Plural-Forms', 'Content-Type'] as $header) {
            if (empty($newHeaders[$header])) {
                $this->addWarning("$header header not declared or empty at byte {$this->position}");
            }
        }
    }

    /**
     * Parses the translation header data into an array
     */
    private function readHeaders(string $string): array
    {
        $headers = [];
        $name = null;
        foreach (array_filter(explode("\n", $string), 'strlen') as $line) {
            // Checks if it is a header definition line.
            // Useful for distinguishing between header definitions and possible continuations of a header entry.
            if (preg_match('/^[\w-]+:/', $line)) {
                [$name, $value] = explode(':', $line, 2);
                if (isset($headers[$name])) {
                    $this->addWarning("Header already defined at byte {$this->position}");
                }
                $headers[$name] = trim($value);
                continue;
            }
            // Data without a definition
            if ($name === null) {
                $this->addWarning("Malformed header name at byte {$this->position}");
                continue;
            }
            $headers[$name] .= $line;
        }

        return $headers;
    }

    /**
     * Ensures the data doesn't start nor end with a newline
     */
    private function checkNewLine(string $data, string $context): void
    {
        if (($first = substr($data, 0, 1)) === "\n" || $first === "\r"
            || ($last = substr($data, -1)) === "\n" || $last === "\n") {
            $this->addWarning("$context cannot start nor end with a newline at byte {$this->position}");
        }
    }

    /**
     * Adds a warning
     */
    private function addWarning(string $message): void
    {
        if ($this->throwOnWarning) {
            throw new Exception($message);
        }
        $this->warnings[] = $message;
    }
}
