<?php
declare(strict_types = 1);

namespace Gettext;

use JsonSerializable;
use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Class to manage the comments of a translation.
 */
class Comments implements JsonSerializable, Countable, IteratorAggregate
{
    protected $comments = [];

    public function __construct(string ...$comments)
    {
        if ($comments) {
            $this->add(...$comments);
        }
    }

    public function add(string ...$comments): self
    {
        foreach ($comments as $comment) {
            if (!in_array($comment, $this->comments)) {
                $this->comments[] = $comment;
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->comments);
    }

    public function count(): int
    {
        return count($this->comments);
    }

    public function toArray(): array
    {
        return $this->comments;
    }

    public function mergeWith(Comments $comments): Comments
    {
        $merged = clone $this;
        $merged->add(...$comments->comments);
        
        return $merged;
    }
}
