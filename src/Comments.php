<?php
declare(strict_types = 1);

namespace Gettext;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use ReturnTypeWillChange;

/**
 * Class to manage the comments of a translation.
 *
 * @phpstan-consistent-constructor
 *
 * @phpstan-type CommentsType array<int, string>
 *
 * @implements IteratorAggregate<int, string>
 */
class Comments implements JsonSerializable, Countable, IteratorAggregate
{
    /**
     * @var CommentsType
     */
    protected $comments = [];

    /**
     * @param array{comments: CommentsType} $state
     */
    public static function __set_state(array $state): Comments
    {
        return new static(...$state['comments']);
    }

    public function __construct(string ...$comments)
    {
        if (!empty($comments)) {
            $this->add(...$comments);
        }
    }

    public function __debugInfo()
    {
        return $this->toArray();
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

    public function delete(string ...$comments): self
    {
        foreach ($comments as $comment) {
            $key = array_search($comment, $this->comments);

            array_splice($this->comments, $key, 1);
        }

        return $this;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->comments);
    }

    public function count(): int
    {
        return count($this->comments);
    }

    /**
     * @return CommentsType
     */
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
