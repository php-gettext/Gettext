<?php
declare(strict_types = 1);

namespace Gettext\Loader;

use Exception;
use Gettext\Translation;
use Gettext\Translations;

/**
 * Class to load a PO file.
 */
final class StrictPoLoader extends Loader
{
    /** @var Translations */
    private $translations;
    /** @var Translation */
    private $translation;
    private $data;
    private $position;
    private $inPreviousComment;

    public function loadString(string $string, Translations $translations = null): Translations
    {
        $this->data = $string;
        $this->position = 0;
        $this->translations = parent::loadString($string, $translations);
        for ($this->newEntry(); $this->getChar() !== null; $this->newEntry()) {
            while ($this->readComment());
            $this->readContext();
            $this->readId();
            if ($this->readPlural()) {
                for ($isRequired = true; $this->readPluralTranslation($isRequired); $isRequired = false);
            } else {
                $this->readTranslation();
            }
            $this->saveEntry();
        }
        $this->processHeader();
        return $this->translations;
    }

    private function newEntry(): void
    {
        $this->inPreviousComment = false;
        $this->translation = $this->createTranslation(null, '');
    }

    private function saveEntry(): void
    {
        $this->translations->add($this->translation);
    }

    private function readDisabledComment(): bool
    {
        return $this->translation->isDisabled() && $this->readString('#~');
    }

    private function readPreviousTranslationComment(): bool
    {
        return $this->inPreviousComment && $this->readString('#|');
    }

    private function readWhiteSpace(): bool
    {
        $position = $this->position;
        while ((ctype_space($this->getChar() ?? '') && $this->nextChar())
            || $this->readDisabledComment()
            || $this->readPreviousTranslationComment());
        return $position !== $this->position;
    }

    private function readString(string $word): bool
    {
        return substr($this->data, $this->position, strlen($word)) === $word
            ? !!($this->position += strlen($word))
            : false;
    }

    private function readChar(string $char): bool
    {
        return $this->getChar() === $char
            ? !!++$this->position
            : false;
    }

    private function nextChar(): ?string
    {
        $char = $this->getChar();
        if ($char !== null) {
            ++$this->position;
        }
        return $char;
    }

    private function getChar(): ?string
    {
        return $this->data[$this->position] ?? null;
    }

    private function readNumber(): string
    {
        for ($data = ''; ctype_digit($this->getChar() ?? ''); $data .= $this->nextChar());
        return $data;
    }

    private function readCommentString(): string
    {
        for ($data = ''; ($this->getChar() ?? "\n") !== "\n"; $data .= $this->nextChar());
        return $data;
    }

    private function readQuotedString(): string
    {
        static $aliases = [
            '\\' => '\\',
            'a' => "\x07",
            'b' => "\x08",
            'e' => "\x1b",
            'f' => "\x0c",
            'n' => "\n",
            'r' => "\r",
            't' => "\t",
            'v' => "\x0b",
            '"' => '"',
        ];
        $hasData = false;
        for ($data = '';;) {
            if (!$this->readChar('"')) {
                if ($hasData) {
                    break;
                }
                throw new Exception("Expected an opening quote at byte {$this->position}");
            }
            for (; ($char = $this->getChar() ?? '"') !== '"'; $data .= $char) {
                $this->nextChar();
                if ($char === '\\') {
                    if (($alias = $aliases[$this->nextChar()] ?? null) === null) {
                        throw new Exception("Invalid quoted character at byte {$this->position}");
                    }
                    $char = $alias;
                    continue;
                }
                if ($char === "\n") {
                    throw new Exception("New line character must be encoded at byte {$this->position}");
                }
            }
            if (!$this->readChar('"')) {
                throw new Exception("Expected an ending quote at byte {$this->position}");
            }
            $this->readWhiteSpace();
            $hasData = true;
        }
        return $data;
    }

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

    private function readIdentifier(string $identifier, bool $isRequired = false): ?string
    {
        $this->readWhiteSpace();
        if (!$this->readString($identifier)) {
            if ($isRequired) {
                throw new Exception("Expected identifier $identifier at byte {$this->position}");
            }
            return null;
        }
        $this->readWhiteSpace();
        return $this->readQuotedString();
    }

    private function readContext(): bool
    {
        if (($data = $this->readIdentifier('msgctxt')) === null) {
            return false;
        }
        $this->translation = $this->translation->withContext($data);
        return true;
    }

    private function readId(): void
    {
        $data = $this->readIdentifier('msgid', true);
        $this->translation = $this->translation->withOriginal($data);
    }

    private function readPlural(): bool
    {
        if (($data = $this->readIdentifier('msgid_plural')) === null) {
            return false;
        }
        $this->translation->setPlural($data);
        return true;
    }

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

    private function readPluralTranslation(bool $isRequired = false): bool
    {
        $this->readWhiteSpace();
        if (!$this->readString('msgstr')) {
            if ($isRequired) {
                throw new Exception("Expected indexed msgstr at byte {$this->position}");
            }
            return false;
        }
        $this->readWhiteSpace();
        if (!$this->readChar('[')) {
            throw new Exception("Expected [ character at byte {$this->position}");
        }
        if (!strlen($index = $this->readNumber())) {
            throw new Exception("Expected msgstr index at byte {$this->position}");
        }
        $this->readWhiteSpace();
        if (!$this->readChar(']')) {
            throw new Exception("Expected ] character at byte {$this->position}");
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

    private function readHeaders(?string $string): array
    {
        if (empty($string)) {
            return [];
        }
        $headers = [];
        $lines = explode("\n", $string);
        $name = null;
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            // Checks if it is a header definition line.
            // Useful for distinguishing between header definitions and possible continuations of a header entry.
            if (preg_match('/^[\w-]+:/', $line)) {
                [$name, $value] = array_map('trim', explode(':', $line, 2));
                $headers[$name] = $value;
                continue;
            }
            // Data without a definition
            if ($name === null) {
                throw new Exception("The header data is missing a definition at byte {$this->position}");
            }
            $value = $headers[$name] ?? '';
            $headers[$name] = $value . $line;
        }
        return $headers;
    }
}
