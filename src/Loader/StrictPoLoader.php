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
    /** @var string */
    private $data;
    /** @var int */
    private $position;
    /** @var bool */
    private $inPreviousComment;

    /**
     * Generates a Translations object from a .po based string
     */
    public function loadString(string $string, Translations $translations = null): Translations
    {
        $this->data = $string;
        $this->position = 0;
        $this->translations = parent::loadString($string, $translations);
        for ($this->newEntry(); $this->getChar() !== null; $this->newEntry()) {
            while ($this->readComment());
            $this->readContext();
            $this->readOriginal();
            if ($this->readPlural() && $this->readPluralTranslation(true)) {
                while ($this->readPluralTranslation());
            } else {
                $this->readTranslation();
            }
            $this->saveEntry();
        }
        $this->processHeader();
        return $this->translations;
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
    private function readWhiteSpace(): bool
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
            ? !!($this->position += strlen($word))
            : false;
    }

    /**
     * Attempts to read the exact informed char
     */
    private function readChar(string $char): bool
    {
        return $this->getChar() === $char ? !!++$this->position : false;
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
     * Read at least one character from the given character set
     */
    private function readCharset(string $charset, int $maxLength): string
    {
        for ($data = ''; ($char = $this->getChar()) !== null && is_int(strpos($charset, $char)) && --$maxLength >= 0; $data .= $this->nextChar());
        if ($data === '') {
            throw new Exception("Expected at least one occurrence of the characters \"{$charset}\" at byte {$this->position}");
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
                'n' => "\n", 'r' => "\r", 't' => "\t", 'v' => "\v", '"' => '"'
            ],
            $octalDigits = '01234567',
            $hexDigits = '0123456789abcdefABCDEF';
        for ($data = '', $pieces = 0;; ++$pieces) {
            if (!$this->readChar('"')) {
                // Perhaps the data is over (e.g. beginning of an identifier), let the next parser decide
                if ($pieces) {
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
                        $data = $octalDigit . $this->readCharset($octalDigits, 2);
                        // GNU gettext fails with octals above the signed char range
                        if (($decimal = octdec($data)) > 127) {
                            throw new Exception("Octal value out of range [0, 0177] at byte {$this->position}");
                        }
                        $char = chr($decimal);
                        break;
                    case 'U':
                    case 'u':
                        // The GNU gettext is supposed to follow the escaping sequences of C
                        // Curiously it doesn't support the unicode escape
                        $data = $this->readCharset($hexDigits, $escaped === 'u' ? 4 : 8);
                        $data = str_pad($data, strlen($data) + (strlen($data) & 1), '0', STR_PAD_LEFT);
                        $char = json_decode("\"\\u{$data}\"");
                        break;
                    case 'x':
                        $data = $this->readCharset($hexDigits, PHP_INT_MAX);
                        // GNU reads all valid hexadecimal chars, but only uses the last pair
                        $char = chr(hexdec(substr($data, -2)));
                        break;
                    default:
                        throw new Exception("Invalid quoted character at byte {$this->position}");
                }
            }
            if (!$this->readChar('"')) {
                throw new Exception("Expected an ending quote at byte {$this->position}");
            }
            $this->readWhiteSpace();
        }
        return $data;
    }

    /**
     * Attempts to read and interpret a comment
     */
    private function readComment(): bool
    {
        $this->readWhiteSpace();
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
                if ($this->inPreviousComment) {
                    throw new Exception("Inconsistent use of #~ at byte {$this->position}");
                }
                $this->translation->disable();
                break;
            case '|':
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
        $this->readWhiteSpace();
        if (!$this->readString($identifier)) {
            if ($throwIfNotFound) {
                throw new Exception("Expected identifier $identifier at byte {$this->position}");
            }
            return null;
        }
        $this->readWhiteSpace();
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
        $this->translation->setPlural($data);
        return true;
    }

    /**
     * Reads the translation
     */
    private function readTranslation(): void
    {
        $this->readWhiteSpace();
        if (!$this->readString('msgstr')) {
            throw new Exception("Expected msgstr at byte {$this->position}");
        }
        $this->readWhiteSpace();
        $data = $this->readQuotedString();
        $this->translation->translate($data);
    }

    /**
     * Attempts to read the pluralized translation
     */
    private function readPluralTranslation(bool $throwIfNotFound = false): bool
    {
        $this->readWhiteSpace();
        if (!$this->readString('msgstr')) {
            if ($throwIfNotFound) {
                throw new Exception("Expected indexed msgstr at byte {$this->position}");
            }
            return false;
        }
        $this->readWhiteSpace();
        if (!$this->readChar('[')) {
            throw new Exception("Expected character \"[\" at byte {$this->position}");
        }
        if (!strlen($index = $this->readNumber())) {
            throw new Exception("Expected msgstr index at byte {$this->position}");
        }
        $this->readWhiteSpace();
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
        $this->readWhiteSpace();
        $data = $this->readQuotedString();
        $translations[] = $data;
        $this->translation->translate(array_shift($translations));
        $this->translation->translatePlural(...$translations);
        return true;
    }

    /**
     * Attempts to find and process the header translation
     */
    private function processHeader(): void
    {
        $translations = $this->translations;
        if (!($header = $translations->find(null, ''))) {
            return;
        }

        $translations->remove($header);
        $description = $header->getComments()->toArray();

        if (!empty($description)) {
            $translations->setDescription(implode("\n", $description));
        }

        $flags = $header->getFlags()->toArray();

        if (!empty($flags)) {
            $translations->getFlags()->add(...$flags);
        }

        $headers = $translations->getHeaders();

        foreach (self::readHeaders($header->getTranslation()) as $name => $value) {
            $headers->set($name, $value);
        }
    }

    /**
     * Parses the translation header data into an array
     */
    private function readHeaders(?string $string): array
    {
        $headers = [];
        $name = null;
        foreach (array_filter(explode("\n", $string), 'strlen') as $line) {
            // Checks if it is a header definition line.
            // Useful for distinguishing between header definitions and possible continuations of a header entry.
            if (preg_match('/^[\w-]+:/', $line)) {
                [$name, $value] = explode(':', $line, 2);
                $headers[$name] = trim($value);
                continue;
            }
            // Data without a definition
            if ($name === null) {
                throw new Exception("The header data is missing a definition at byte {$this->position}");
            }
            $headers[$name] .= $line;
        }
        return $headers;
    }
}
