<?php
declare(strict_types = 1);

namespace Gettext\Scanner;

/**
 * Class to handle the info of a parsed comment
 */
final class ParsedComment
{
    private $comment;
    private $filename;
    private $line;
    private $lastLine;

    public function __construct(string $comment, string $filename, int $line)
    {
        $this->filename = $filename;
        $this->line = $this->lastLine = $line;
        $this->setComment($comment);
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    private function setComment(string $comment)
    {
        $lines = array_map(function ($line) {
            $line = ltrim($line, "#*/ \t");
            $line = rtrim($line, "#*/ \t");

            return trim($line);
        }, explode("\n", $comment));

        $this->lastLine = $this->line + count($lines) - 1;
        $this->comment = trim(implode("\n", $lines));
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getLastLine(): int
    {
        return $this->lastLine;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Whether this comment is related with a given function.
     */
    public function isRelatedWith(ParsedFunction $function): bool
    {
        if ($this->getFilename() !== $function->getFilename()) {
            return false;
        }

        if ($this->getLastLine() < $function->getLine() - 1) {
            return false;
        }

        if ($this->getLine() > $function->getLastLine()) {
            return false;
        }

        return true;
    }

    /**
     * Whether the comment matches the required prefixes.
     */
    public function isPrefixed(array $prefixes): bool
    {
        if ($this->getComment() === '' || empty($prefixes)) {
            return false;
        }

        foreach ($prefixes as $prefix) {
            if (strpos($this->comment, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }
}
