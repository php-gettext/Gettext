<?php
declare(strict_types = 1);

namespace Gettext;

use JsonSerializable;
use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Class to manage the flags of a translation.
 */
class Flags implements JsonSerializable, Countable, IteratorAggregate
{
    protected $flags = [];

    public function add(string $flag): self
    {
        if (!in_array($flag, $this->flags)) {
            $this->flags[] = $flag;
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->flags);
    }

    public function count(): int
    {
        return count($this->flags);
    }

    public function toArray(): array
    {
        return $this->flags;
    }
}
