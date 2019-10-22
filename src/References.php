<?php
declare(strict_types = 1);

namespace Gettext;

use JsonSerializable;
use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Class to manage the references of a translation.
 */
class References implements JsonSerializable, Countable, IteratorAggregate
{
    protected $references = [];

    public function add(string $filename, ?int $line): self
    {
        $fileReferences = $this->references[$filename] ?? [];

        if (isset($line) && !in_array($line, $fileReferences)) {
            $fileReferences[] = $line;
        }

        $this->references[$filename] = $fileReferences;

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->references);
    }

    public function count(): int
    {
        return array_reduce($this->references, function ($carry, $item) {
            return $carry + (count($item) ?: 1);
        }, 0);
    }

    public function toArray(): array
    {
        return $this->references;
    }
}
