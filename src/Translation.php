<?php
declare(strict_types = 1);

namespace Gettext;

/**
 * Class to manage an individual translation
 */
class Translation
{
    protected $id;
    protected $context;
    protected $original;
    protected $translation;
    protected $plural;
    protected $pluralTranslations = [];
    protected $disabled = false;
    protected $references;
    protected $flags;
    protected $comments;
    protected $extractedComments;

    public static function create(?string $context, string $original): Translation
    {
        $id = static::generateId($context, $original);

        $translation = new static($id);
        $translation->context = $context;
        $translation->original = $original;

        return $translation;
    }

    protected static function generateId(?string $context, string $original): string
    {
        return "{$context}\004{$original}";
    }

    protected function __construct(string $id)
    {
        $this->id = $id;

        $this->references = new References();
        $this->flags = new Flags();
        $this->comments = new Comments();
        $this->extractedComments = new Comments();
    }

    public function __clone()
    {
        $this->references = clone $this->references;
        $this->flags = clone $this->flags;
        $this->comments = clone $this->comments;
        $this->extractedComments = clone $this->extractedComments;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function withContext(?string $context): Translation
    {
        $clone = clone $this;
        $clone->context = $context;
        $clone->id = static::generateId($clone->getContext(), $clone->getOriginal());

        return $clone;
    }

    public function getOriginal(): ?string
    {
        return $this->original;
    }

    public function withOriginal(string $original): Translation
    {
        $clone = clone $this;
        $clone->original = $original;
        $clone->id = static::generateId($clone->getContext(), $clone->getOriginal());

        return $clone;
    }

    public function setPlural(string $plural): self
    {
        $this->plural = $plural;

        return $this;
    }

    public function getPlural(): ?string
    {
        return $this->plural;
    }

    public function disable(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function translate(string $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function isTranslated(): bool
    {
        return isset($this->translation) && $this->translation !== '';
    }

    public function translatePlural(string ...$translations): self
    {
        $this->pluralTranslations = $translations;

        return $this;
    }

    public function getPluralTranslations(int $size = null): array
    {
        if ($size === null) {
            return $this->pluralTranslations;
        }

        $length = count($this->pluralTranslations);

        if ($size > $length) {
            return $this->pluralTranslations + array_fill(0, $size, '');
        }

        return array_slice($this->pluralTranslations, 0, $size);
    }

    public function getReferences(): References
    {
        return $this->references;
    }

    public function getFlags(): Flags
    {
        return $this->flags;
    }

    public function getComments(): Comments
    {
        return $this->comments;
    }

    public function getExtractedComments(): Comments
    {
        return $this->extractedComments;
    }
}
