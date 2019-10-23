<?php
declare(strict_types = 1);

namespace Gettext\Scanner;

/**
 * Class to handle the info of a parsed function
 */
final class ParsedFunction
{
    private $name;
    private $filename;
    private $line;
    private $lastLine;
    private $arguments = [];
    private $comments = [];
    private $argumentIsClosed = false;

    public function __construct(string $name, string $filename, int $line)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->line = $this->lastLine = $line;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getLastLine(): int
    {
        return $this->lastLine;
    }

    public function setLastLine(int $lastLine): self
    {
        $this->lastLine = $lastLine;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function countArguments(): int
    {
        return count($this->arguments);
    }

    /**
     * @return ParsedComments[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    public function addArgument(string $argument = null): self
    {
        $this->arguments[] = $argument;
        $this->argumentIsClosed = false;

        return $this;
    }

    public function addArgumentChunk(string $chunk): self
    {
        if ($this->argumentIsClosed) {
            return $this;
        }

        $value = end($this->arguments).$chunk;
        $key = key($this->arguments) ?: 0;

        $this->arguments[$key] = $value;

        return $this;
    }

    public function closeArgument(): self
    {
        $this->argumentIsClosed = true;

        return $this;
    }

    public function addComment(ParsedComment $comment): self
    {
        $this->comments[] = $comment;

        return $this;
    }
}
