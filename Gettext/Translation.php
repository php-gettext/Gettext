<?php
namespace Gettext;

class Translation {
	public $context;
	public $original;
	public $translation;
	public $plural;
	public $pluralTranslation = array();
	public $references = array();
	public $comments = array();

	public function __construct ($context = null, $original = null, $translation = null) {
		$this->setContext($context);
		$this->setOriginal($original);
		$this->setTranslation($translation);
	}

	public function is ($context, $original = null, $plural = null) {
		return (($this->context === $context) && ($this->original === $original) && ($this->plural === $plural)) ? true : false;
	}

	//ORIGINAL STRING
	public function setOriginal ($original) {
		$this->original = $original;
	}

	public function getOriginal () {
		return $this->original;
	}

	public function hasOriginal () {
		return (isset($this->original) && ($this->original !== '')) ? true : false;
	}

	//TRANSLATED STRING
	public function setTranslation ($translation) {
		$this->translation = $translation;
	}

	public function getTranslation () {
		return $this->translation;
	}

	public function hasTranslation () {
		return (isset($this->translation) && ($this->translation !== '')) ? true : false;
	}

	//PLURAL STRING
	public function setPlural ($plural) {
		$this->plural = $plural;
	}

	public function getPlural () {
		return $this->plural;
	}

	public function hasPlural () {
		return (isset($this->plural) && ($this->plural !== '')) ? true : false;
	}

	//PLURAL TRANSLATED STRINGS
	public function setPluralTranslation ($plural, $key = null) {
		if (isset($key)) {
			$this->pluralTranslation[$key] = $plural;
		} else {
			$this->pluralTranslation[] = $plural;
		}
	}

	public function getPluralTranslation ($key = null) {
		if (isset($key)) {
			return isset($this->pluralTranslation[$key]) ? $this->pluralTranslation[$key] : null;
		}

		return $this->pluralTranslation;
	}

	public function hasPluralTranslation () {
		return isset($this->pluralTranslation[0]);
	}

	//CONTEXT
	public function setContext ($context) {
		$this->context = $context;
	}

	public function getContext () {
		return $this->context;
	}

	public function hasContext () {
		return (isset($this->context) && ($this->context !== '')) ? true : false;
	}

	//REFERENCES
	public function addReference ($filename, $line) {
		$this->references[] = array($filename, $line);
	}

	public function hasReferences () {
		return isset($this->references[0]);
	}

	public function getReferences () {
		return $this->references;
	}

	//COMMENTS
	public function addComment ($comment) {
		$this->comments[] = $comment;
	}

	public function hasComments () {
		return isset($this->comments[0]);
	}

	public function getComments () {
		return $this->comments;
	}
}
