<?php declare(strict_types=1);

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

    public static function __set_state(array $state): References
    {
        $references = new static();
        $references->references = $state['references'];

        return $references;
    }

    public function __debugInfo()
    {
        return $this->toArray();
    }

    public function add(string $filename, int $line = null): self
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

    public function mergeWith(References $references): References
    {
        $merged = clone $this;

        foreach ($references as $filename => $lines) {
            if (empty($lines)) {
                $merged->add($filename);
                continue;
            }

            foreach ($lines as $line) {
                $merged->add($filename, $line);
            }
        }

        return $merged;
    }
}
