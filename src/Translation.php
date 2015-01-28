<?php
namespace Gettext;

/**
 * Class to manage a translation string
 */
class Translation
{
    protected $context;
    protected $original;
    protected $translation = '';
    protected $plural;
    protected $pluralTranslation = array();
    protected $references = array();
    protected $comments = array();
    protected $extractedComments = array();
    protected $flags = array();
    protected $translationCount;

    /**
     * Generates the id of a translation (context + glue + original)
     *
     * @param string $context
     * @param string $original
     *
     * @return string
     */
    public static function generateId($context, $original)
    {
        return "{$context}\004{$original}";
    }

    /**
     * Construct
     *
     * @param string $context  The context of the translation
     * @param string $original The original string
     * @param string $plural   The original plural string
     */
    public function __construct($context, $original, $plural = '')
    {
        $this->context = (string) $context;
        $this->original = (string) $original;

        $this->setPlural($plural);
    }

    /**
     * Clones this translation
     *
     * @param null|string $context  Optional new context
     * @param null|string $original Optional new original
     */
    public function getClone($context = null, $original = null)
    {
        $new = clone $this;

        if ($context !== null) {
            $new->context = (string) $context;
        }

        if ($original !== null) {
            $new->original = (string) $original;
        }

        return $new;
    }

    /**
     * Returns the id of this translation
     *
     * @return string
     */
    public function getId()
    {
        return static::generateId($this->context, $this->original);
    }

    /**
     * Checks whether the translation matches with the arguments
     *
     * @param string $context
     * @param string $original
     *
     * @return boolean
     */
    public function is($context, $original = '')
    {
        return (($this->context === $context) && ($this->original === $original)) ? true : false;
    }

    /**
     * Gets the original string
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Checks if the original string is empty or not
     *
     * @return boolean
     */
    public function hasOriginal()
    {
        return ($this->original !== '') ? true : false;
    }

    /**
     * Sets the translation string
     *
     * @param string $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = (string) $translation;
    }

    /**
     * Gets the translation string
     *
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Checks if the translation string is empty or not
     *
     * @return boolean
     */
    public function hasTranslation()
    {
        return ($this->translation !== '') ? true : false;
    }

    /**
     * Sets the plural translation string
     *
     * @param string $plural
     */
    public function setPlural($plural)
    {
        $this->plural = (string) $plural;

        $this->normalizeTranslationCount();
    }

    /**
     * Gets the plural translation string
     *
     * @return string
     */
    public function getPlural()
    {
        return $this->plural;
    }

    /**
     * Checks if the plural translation string is empty or not
     *
     * @return boolean
     */
    public function hasPlural()
    {
        return ($this->plural !== '') ? true : false;
    }

    /**
     * Set a new plural translation
     *
     * @param string  $plural The plural string to add
     * @param integer $key    The key of the plural translation.
     */
    public function setPluralTranslation($plural, $key = 0)
    {
        $this->pluralTranslation[$key] = $plural;
        $this->normalizeTranslationCount();
    }

    /**
     * Gets one or all plural translations
     *
     * @param integer|null $key The key to return. If is null, return all translations
     *
     * @return string|array
     */
    public function getPluralTranslation($key = null)
    {
        if ($key === null) {
            return $this->pluralTranslation;
        }

        return isset($this->pluralTranslation[$key]) ? (string) $this->pluralTranslation[$key] : '';
    }

    /**
     * Checks if there are any plural translation
     *
     * @return boolean
     */
    public function hasPluralTranslation()
    {
        return implode('', $this->pluralTranslation) !== '';
    }

    /**
     * Removes all plural translations
     */
    public function deletePluralTranslation()
    {
        $this->pluralTranslation = array();

        $this->normalizeTranslationCount();
    }

    /**
     * Set the number of singular + plural translations allowed
     *
     * @param integer $count
     */
    public function setTranslationCount($count)
    {
        $this->translationCount = is_null($count) ? null : intval($count);

        $this->normalizeTranslationCount();
    }

    /**
     * Returns the number of singular + plural translations
     * Returns null if this Translation is not a plural one
     *
     * @return integer|null
     */
    public function getTranslationCount()
    {
        return $this->hasPlural() ? $this->translationCount : null;
    }

    /**
     * Normalizes the translation count
     */
    protected function normalizeTranslationCount()
    {
        if ($this->translationCount === null) {
            return;
        }

        if ($this->hasPlural()) {
            $allowed = $this->translationCount - 1;
            $current = count($this->pluralTranslation);

            if ($allowed > $current) {
                $this->pluralTranslation = $this->pluralTranslation + array_fill(0, $allowed, '');
            } elseif ($current > $allowed) {
                $this->pluralTranslation = array_slice($this->pluralTranslation, 0, $allowed);
            }
        } else {
            $this->pluralTranslation = array();
        }
    }

    /**
     * Gets the context of this translation
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Checks if the context is empty or not
     *
     * @return boolean
     */
    public function hasContext()
    {
        return (isset($this->context) && ($this->context !== '')) ? true : false;
    }

    /**
     * Adds a new reference for this translation
     *
     * @param string       $filename The file path where the translation has been found
     * @param null|integer $line     The line number where the translation has been found
     */
    public function addReference($filename, $line = null)
    {
        $key = "{$filename}:{$line}";
        $this->references[$key] = array($filename, $line);
    }

    /**
     * Checks if the translation has any reference
     *
     * @return boolean
     */
    public function hasReferences()
    {
        return !empty($this->references);
    }

    /**
     * Return all references for this translation
     *
     * @return array
     */
    public function getReferences()
    {
        return array_values($this->references);
    }

    /**
     * Removes all references
     */
    public function deleteReferences()
    {
        $this->references = array();
    }

    /**
     * Adds a new comment for this translation
     *
     * @param string $comment
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * Checks if the translation has any comment
     *
     * @return boolean
     */
    public function hasComments()
    {
        return isset($this->comments[0]);
    }

    /**
     * Returns all comments for this translation
     *
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Removes all comments
     */
    public function deleteComments()
    {
        $this->comments = array();
    }

    /**
     * Adds a new extracted comment for this translation
     *
     * @param string $comment
     */
    public function addExtractedComment($comment)
    {
        $this->extractedComments[] = $comment;
    }

    /**
     * Checks if the translation has any extracted comment
     *
     * @return boolean
     */
    public function hasExtractedComments()
    {
        return isset($this->extractedComments[0]);
    }

    /**
     * Returns all extracted comments for this translation
     *
     * @return array
     */
    public function getExtractedComments()
    {
        return $this->extractedComments;
    }

    /**
     * Removes all extracted comments
     */
    public function deleteExtractedComments()
    {
        $this->extractedComments = array();
    }

    /**
     * Adds a new flat for this translation
     *
     * @param string $flag
     */
    public function addFlag($flag)
    {
        $this->flags[] = $flag;
    }

    /**
     * Checks if the translation has any flag
     *
     * @return boolean
     */
    public function hasFlags()
    {
        return isset($this->flags[0]);
    }

    /**
     * Returns all extracted flags for this translation
     *
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Removes all flags
     */
    public function deleteFlags()
    {
        $this->flags = array();
    }

    /**
     * Merges this translation with other translation
     *
     * @param Translation  $translation The translation to merge with
     * @param integer|null $method      One or various Translations::MERGE_* constants to define how to merge the translations
     */
    public function mergeWith(Translation $translation, $method = null)
    {
        if ($method === null) {
            $method = Translations::$mergeDefault;
        }

        if (!$this->hasTranslation()) {
            $this->setTranslation($translation->getTranslation());
        }

        if (($method & Translations::MERGE_PLURAL) && !$this->hasPlural()) {
            $this->setPlural($translation->getPlural());
        }

        if ($this->hasPlural() && !$this->hasPluralTranslation() && $translation->hasPluralTranslation()) {
            $this->pluralTranslation = $translation->getPluralTranslation();
        }

        if ($method & Translations::MERGE_REFERENCES) {
            foreach ($translation->getReferences() as $reference) {
                $this->addReference($reference[0], $reference[1]);
            }
        }

        if ($method & Translations::MERGE_COMMENTS) {
            $this->comments = array_values(array_unique(array_merge($translation->getComments(), $this->comments)));
            $this->extractedComments = array_values(array_unique(array_merge($translation->getExtractedComments(), $this->extractedComments)));
            $this->flags = array_values(array_unique(array_merge($translation->getFlags(), $this->flags)));
        }
    }
}
