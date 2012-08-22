<?php
namespace Gettext;

class Translation {
	public $domain;
	public $context;
	public $original;
	public $translation;
	public $plural;
	public $pluralTranslation = array();
	public $references = array();
	public $comments = array();

	public function __construct ($original = null, $translation = null, $context = null, $domain = null) {
		$this->setOriginal($original);
		$this->setTranslation($translation);
		$this->setContext($context);
		$this->setDomain($domain);
	}

	public function is ($original, $translation = null, $context = null, $domain = null) {
		return ($this->original === $original && $this->translation === $translation && $this->context === $context && $this->domain === $domain) ? true : false;
	}

	//ORIGINAL STRING
	public function setOriginal ($original) {
		$this->original = $original;
	}

	public function getOriginal () {
		return $this->original;
	}

	public function hasOriginal () {
		return (isset($this->original) && $this->original !== '') ? true : false;
	}

	//TRANSLATED STRING
	public function setTranslation ($translation) {
		$this->translation = $translation;
	}

	public function getTranslation () {
		return $this->translation;
	}

	public function hasTranslation () {
		return (isset($this->translation) && $this->translation !== '') ? true : false;
	}

	//PLURAL STRING
	public function setPlural ($plural) {
		$this->plural = $plural;
	}

	public function getPlural () {
		return $this->plural;
	}

	public function hasPlural () {
		return (isset($this->plural) && $this->plural !== '') ? true : false;
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

	//DOMAIN
	public function setDomain ($domain) {
		$this->domain = $domain;
	}

	public function getDomain () {
		return $this->domain;
	}

	public function hasDomain () {
		return (isset($this->domain) && $this->domain !== '') ? true : false;
	}

	//CONTEXT
	public function setContext ($context) {
		$this->context = $context;
	}

	public function getContext () {
		return $this->context;
	}

	public function hasContext () {
		return (isset($this->context) && $this->context !== '') ? true : false;
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
