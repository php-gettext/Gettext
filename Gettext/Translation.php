<?php
namespace Gettext;

class Translation {
	public $context = '';
	public $original = '';
	public $translation = '';
	public $plural = '';
	public $pluralTranslation = array();
	public $references = array();
	public $comments = array();

	public function __construct ($context = null, $original = null, $plural = null) {
		$this->setContext($context);
		$this->setOriginal($original);
		$this->setPlural($plural);
	}

	public function is ($context, $original = null, $plural = null) {
		return (($this->context === $context) && ($this->original === $original) && ($this->plural === $plural)) ? true : false;
	}

	//ORIGINAL STRING
	public function setOriginal ($original) {
		$this->original = (string)$original;
	}

	public function getOriginal () {
		return $this->original;
	}

	public function hasOriginal () {
		return ($this->original !== '') ? true : false;
	}

	//TRANSLATED STRING
	public function setTranslation ($translation) {
		$this->translation = (string)$translation;
	}

	public function getTranslation () {
		return $this->translation;
	}

	public function hasTranslation () {
		return ($this->translation !== '') ? true : false;
	}

	//PLURAL STRING
	public function setPlural ($plural) {
		$this->plural = (string)$plural;
	}

	public function getPlural () {
		return $this->plural;
	}

	public function hasPlural () {
		return ($this->plural !== '') ? true : false;
	}

	//PLURAL TRANSLATED STRINGS
	public function setPluralTranslation ($plural, $key = null) {
		if ($key === null) {
			$this->pluralTranslation[] = $plural;
		} else {
			$this->pluralTranslation[$key] = $plural;
		}
	}

	public function getPluralTranslation ($key = null) {
		if ($key === null) {
			return $this->pluralTranslation;
		}

		return isset($this->pluralTranslation[$key]) ? (string)$this->pluralTranslation[$key] : '';
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
