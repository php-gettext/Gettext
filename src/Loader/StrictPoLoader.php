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
    /** @var bool */
    public $throwOnWarning = false;
    /** @var bool */
    public $displayErrorLine = false;

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
    private $inPreviousPart;
    /** @var string[] */
    private $warnings = [];
    /** @var bool */
    private $isDisabled;
    /** @var bool */
    private $displayLineColumn;

    /**
     * Generates a Translations object from a .po based string
     */
    public function loadString(string $data, Translations $translations = null): Translations
    {
        $this->data = $data;
        $this->position = 0;
        $this->translations = parent::loadString($this->data, $translations);
        $this->header = $this->translations->find(null, '');
        $this->pluralCount = $this->translations->getHeaders()->getPluralForm()[0] ?? null;
        $this->warnings = [];
        for ($length = strlen($this->data); $this->newEntry(); $this->saveEntry()) {
            for ($hasComment = false; $this->readComment(); $hasComment = true);
            $this->readWhitespace();
            // End of data
            if ($this->position >= $length) {
                if ($hasComment) {
                    $this->addWarning("Comment ignored at the end of the string{$this->getErrorPosition()}");
                }
                break;
            }
            $this->readContext();
            $this->readOriginal();
            if ($this->translations->has($this->translation)) {
                throw new Exception("Duplicated entry{$this->getErrorPosition()}");
            }
            if (!$this->readPlural()) {
                $this->readTranslation();
                continue;
            }
            for ($count = 0; $this->readPluralTranslation(!$count); ++$count);
            $count !== ($this->pluralCount ?? $count) && $this->addWarning("The translation has {$count} plural "
                . "forms, while the header expects {$this->pluralCount}{$this->getErrorPosition()}");
        }
        if (!$this->header) {
            $this->addWarning("The loaded string has no header translation{$this->getErrorPosition()}");
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
    private function newEntry(): Translation
    {
        $this->isDisabled = false;

        return $this->translation = $this->createTranslation(null, '');
    }

    /**
     * Adds the current translation to the output list
     */
    private function saveEntry(): void
    {
        if ($this->isHeader()) {
            $this->processHeader();

            return;
        }
        $this->translations->add($this->translation);
    }

    /**
     * Attempts to read whitespace characters, also might skip complex comment prologs when needed
     * @return int The position before comments started being consumed
     */
    private function readWhitespace(): int
    {
        do {
            $this->position += strspn($this->data, " \n\r\t\v\0", $this->position);
            $checkpoint ?? $checkpoint = $this->position;
        } while (($this->isDisabled && $this->readString('#~')) || ($this->inPreviousPart && $this->readString('#|')));

        return $checkpoint;
    }

    /**
     * Attempts to read the exact informed string
     */
    private function readString(string $data): bool
    {
        return !substr_compare($this->data, $data, $this->position, $l = strlen($data)) && $this->position += $l;
    }

    /**
     * Attempts to read the exact informed char
     */
    private function readChar(string $char): bool
    {
        return ($this->data[$this->position] ?? null) === $char && ++$this->position;
    }

    /**
     * Read sequential characters that match the given character set until the length range is satisfied
     */
    private function readCharset(string $charset, int $min, int $max, string $name): string
    {
        if (($length = strspn($this->data, $charset, $this->position, $max)) < $min) {
            throw new Exception("Expected at least {$min} occurrence of {$name} characters{$this->getErrorPosition()}");
        }

        return substr($this->data, ($this->position += $length) - $length, $length);
    }

    /**
     * Attempts to read a standard comment string which ends with a newline
     */
    private function readCommentString(): string
    {
        $length = strcspn($this->data, "\n\r", $this->position);

        return substr($this->data, ($this->position += $length) - $length, $length);
    }

    /**
     * Attempts to read a quoted string while parsing escape sequences prefixed by \
     */
    private function readQuotedString(?string $context = null): string
    {
        $this->readWhitespace();
        for ($data = '', $isNewPart = true, $checkpoint = null;;) {
            if ($isNewPart && !$this->readChar('"')) {
                // The data is over (e.g. beginning of an identifier) or perhaps there's an error
                // Restore the checkpoint and let the next parser handle it
                if ($checkpoint !== null) {
                    $this->position = $checkpoint;
                    break;
                }
                throw new Exception("Expected an opening quote{$this->getErrorPosition()}");
            }
            $isNewPart = false;
            // Collects chars until an edge case is found
            $length = strcspn($this->data, "\"\r\n\\", $this->position);
            $data .= substr($this->data, $this->position, $length);
            $this->position += $length;
            // Check edge cases
            switch ($this->data[$this->position++] ?? null) {
                // End of part, saves a checkpoint and attempts to read a new part
                case '"':
                    $checkpoint = $this->readWhitespace();
                    $isNewPart = true;
                    break;
                case '\\':
                    $data .= $this->readEscape();
                    break;
                // Unexpected newline
                case "\r":
                case "\n":
                    throw new Exception("Newline character must be escaped{$this->getErrorPosition()}");
                // Unexpected end of file
                case null:
                    throw new Exception("Expected a closing quote{$this->getErrorPosition()}");
            }
        }
        if ($context && strlen($data) && strpbrk($data[0] . $data[strlen($data) - 1], "\r\n") && !$this->isHeader()) {
            $this->addWarning("$context cannot start nor end with a newline{$this->getErrorPosition()}");
        }

        return $data;
    }

    /**
     * Reads escaped data
     */
    private function readEscape(): string
    {
        $aliasMap = ['from' => 'efnrtv"ab\\', 'to' => "\e\f\n\r\t\v\"\x07\x08\\"];
        $hexDigits = '0123456789abcdefABCDEF';
        switch ($char = $this->data[$this->position++] ?? "\0") {
            case strpbrk($char, $aliasMap['from']) ?: '':
                return $aliasMap['to'][strpos($aliasMap['from'], $char)];
            case strpbrk($char, $octalDigits = '01234567'):
                // GNU gettext fails with an octal above the signed char range
                if (($decimal = octdec($char . $this->readCharset($octalDigits, 0, 2, 'octal'))) > 127) {
                    throw new Exception("Octal value out of range [0, 0177]{$this->getErrorPosition()}");
                }

                return chr($decimal);
            case 'x':
                $value = $this->readCharset($hexDigits, 1, PHP_INT_MAX, 'hexadecimal');
                // GNU reads all valid hexadecimal chars, but only uses the last pair
                return hex2bin(str_pad(substr($value, -2), 2, '0', STR_PAD_LEFT));
            case 'U':
            case 'u':
                // The GNU gettext is supposed to follow the escaping sequences of C
                // Curiously it doesn't support the unicode escape
                $value = $this->readCharset($hexDigits, 1, $digits = $char === 'u' ? 4 : 8, 'hexadecimal');
                $value = str_pad($value, $digits, '0', STR_PAD_LEFT);

                return mb_convert_encoding(hex2bin($value), 'UTF-8', 'UTF-' . ($digits * 4));
        }
        throw new Exception("Invalid escaped character{$this->getErrorPosition()}");
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
        $type = strpbrk($this->data[$this->position] ?? '', '~|,:.') ?: '';
        $this->position += strlen($type);
        // Only a single space might be optionally added
        $this->readChar(' ');
        switch ($type) {
            case '':
                $data = $this->readCommentString();
                $this->translation->getComments()->add($data);
                break;
            case '~':
                if ($this->translation->getPreviousOriginal() !== null) {
                    throw new Exception("Inconsistent use of #~{$this->getErrorPosition()}");
                }
                $this->translation->disable();
                $this->isDisabled = true;
                break;
            case '|':
                if ($this->translation->getPreviousOriginal() !== null) {
                    throw new Exception('Cannot redeclare the previous comment #|, '
                        . "ensure the definitions are in the right order{$this->getErrorPosition()}");
                }
                $this->inPreviousPart = true;
                $this->translation->setPreviousContext($this->readIdentifier('msgctxt'));
                $this->translation->setPreviousOriginal($this->readIdentifier('msgid', true));
                $this->translation->setPreviousPlural($this->readIdentifier('msgid_plural'));
                $this->inPreviousPart = false;
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
        $checkpoint = $this->readWhitespace();
        if ($this->readString($identifier)) {
            return $this->readQuotedString($identifier);
        }
        if ($throwIfNotFound) {
            throw new Exception("Expected $identifier{$this->getErrorPosition()}");
        }
        $this->position = $checkpoint;

        return null;
    }

    /**
     * Attempts to read the context
     */
    private function readContext(): bool
    {
        return ($data = $this->readIdentifier('msgctxt')) !== null
            && ($this->translation = $this->translation->withContext($data));
    }

    /**
     * Reads the original message
     */
    private function readOriginal(): void
    {
        $this->translation = $this->translation->withOriginal($this->readIdentifier('msgid', true));
    }

    /**
     * Attempts to read the plural message
     */
    private function readPlural(): bool
    {
        return ($data = $this->readIdentifier('msgid_plural')) !== null && $this->translation->setPlural($data);
    }

    /**
     * Reads the translation
     */
    private function readTranslation(): void
    {
        $this->readWhitespace();
        if (!$this->readString('msgstr')) {
            throw new Exception("Expected msgstr{$this->getErrorPosition()}");
        }
        $this->translation->translate($this->readQuotedString('msgstr'));
    }

    /**
     * Attempts to read the pluralized translation
     */
    private function readPluralTranslation(bool $throwIfNotFound = false): bool
    {
        $this->readWhitespace();
        if (!$this->readString('msgstr')) {
            if ($throwIfNotFound) {
                throw new Exception("Expected indexed msgstr{$this->getErrorPosition()}");
            }

            return false;
        }
        $this->readWhitespace();
        if (!$this->readChar('[')) {
            throw new Exception("Expected character \"[\"{$this->getErrorPosition()}");
        }
        $this->readWhitespace();
        $index = (int) $this->readCharset('0123456789', 1, PHP_INT_MAX, 'numeric');
        $this->readWhitespace();
        if (!$this->readChar(']')) {
            throw new Exception("Expected character \"]\"{$this->getErrorPosition()}");
        }
        $translations = $this->translation->getPluralTranslations();
        if (($translation = $this->translation->getTranslation()) !== null) {
            array_unshift($translations, $translation);
        }
        if (count($translations) !== (int) $index) {
            throw new Exception("The msgstr has an invalid index{$this->getErrorPosition()}");
        }
        $data = $this->readQuotedString('msgstr');
        $translations[] = $data;
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
        if (count($description = $header->getComments()->toArray())) {
            $this->translations->setDescription(implode("\n", $description));
        }
        if (count($flags = $header->getFlags()->toArray())) {
            $this->translations->getFlags()->add(...$flags);
        }
        $headers = $this->translations->getHeaders();
        if (($header->getTranslation() ?? '') !== '') {
            foreach (self::readHeaders($header->getTranslation()) as $name => $value) {
                $headers->set($name, $value);
            }
        }
        $this->pluralCount = $headers->getPluralForm()[0] ?? null;
        foreach (['Language', 'Plural-Forms', 'Content-Type'] as $header) {
            if (($headers->get($header) ?? '') === '') {
                $this->addWarning("$header header not declared or empty{$this->getErrorPosition()}");
            }
        }
    }

    /**
     * Parses the translation header data into an array
     */
    private function readHeaders(string $data): array
    {
        $headers = [];
        $name = null;
        foreach (explode("\n", $data) as $line) {
            // Checks if it is a header definition line.
            // Useful for distinguishing between header definitions and possible continuations of a header entry.
            if (preg_match('/^[\w-]+:/', $line)) {
                [$name, $value] = explode(':', $line, 2);
                if (isset($headers[$name])) {
                    $this->addWarning("Header already defined{$this->getErrorPosition()}");
                }
                $headers[$name] = trim($value);
                continue;
            }
            // Data without a definition
            if ($name === null) {
                $this->addWarning("Malformed header name{$this->getErrorPosition()}");
                continue;
            }
            $headers[$name] .= $line;
        }

        return $headers;
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

    /**
     * Checks whether the current translation is a header translation
     */
    private function isHeader(): bool
    {
        return $this->translation->getOriginal() === '' && $this->translation->getContext() === null;
    }

    /**
     * Retrieves the position where an error was detected
     */
    private function getErrorPosition(): string
    {
        if ($this->displayErrorLine) {
            $pieces = preg_split('/\\r\\n|\\n\\r|\\n|\\r/', substr($this->data, 0, $this->position));
            $line = count($pieces);
            $column = strlen(end($pieces));

            return " at line {$line} column {$column}";
        }

        return " at byte {$this->position}";
    }
}
