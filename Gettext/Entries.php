<?php
namespace Gettext;

use Gettext\Translation;

class Entries extends \ArrayObject {
	public $domain = null;
	public $headers = array();

	public function setHeader ($name, $value) {
		$this->headers[trim($name)] = trim($value);
	}

	public function getHeader ($name) {
		return isset($this->headers[$name]) ? $this->headers[$name] : null;
	}

	public function getHeaders () {
		return $this->headers;
	}

	public function setDomain ($domain) {
		$this->domain = $domain;
	}

	public function getDomain () {
		return $this->domain;
	}

	public function hasDomain () {
		return (isset($this->domain) && $this->domain !== '') ? true : false;
	}

	public function find ($original, $translation = null, $context = null, $domain = null) {
		if (is_object($original) && $original instanceof Translation) {
			$domain = $original->getDomain();
			$context = $original->getContext();
			$translation = $original->getTranslation();
			$original = $original->getOriginal();
		}

		foreach ($this as $t) {
			if ($t->is($original, $translation, $context, $domain)) {
				return $t;
			}
		}

		return false;
	}

	public function append ($original, $translation = null, $context = null, $domain = null) {
		return $this[] = new Translation($original, $translation, $context, $domain);
	}
}
