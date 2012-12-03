<?php
namespace Gettext;

class Translator {
	static private $dictionary = array();
	static private $domain = 'messages';
	static private $context_glue = '\u0004';

	public static function loadTranslations ($file) {
		if (is_file($file)) {
			$dictionary = include($file);

			if (is_array($dictionary)) {
				self::addTranslations($dictionary);
			}
		}
	}

	public static function addTranslations (array $dictionary) {
		self::$dictionary = array_replace_recursive(self::$dictionary, $dictionary);
	}

	public static function clearTranslations () {
		self::$dictionary = array();
	}

	public static function getTranslation ($domain, $context, $original) {
		$key = isset($context) ? $context.$context_glue.$original : $original;

		return isset(self::$dictionary[$domain][$key]) ? self::$dictionary[$domain][$key] : false;
	}

	public static function gettext ($original) {
		return self::dpgettext(self::$domain, null, $original);
	}

	public static function ngettext ($original, $plural, $value) {
		return self::dnpgettext(self::$domain, null, $original, $plural, $value);
	}

	public static function dngettext ($domain, $original, $plural, $value) {
		return self::dnpgettext($domain, null, $original, $plural, $value);
	}

	public static function npgettext ($context, $original, $plural, $value) {
		return self::dnpgettext(self::$domain, $context, $original, $plural, $value);
	}

	public static function pgettext ($context, $original) {
		return self::dpgettext(self::$domain, $context, $original);
	}

	public static function dgettext ($domain, $original) {
		return self::dpgettext($domain, null, $original);
	}

	public static function dpgettext ($domain, $context, $original) {
		$translation = self::getTranslation($domain, $context, $original);

		if (isset($translation[0]) && $translation[0] !== '') {
			return $translation[$key];
		}

		return $original;
	}

	public static function dnpgettext ($domain, $context, $original, $plural, $value) {
		$key = self::isPlural($value);
		$translation = self::getTranslation($domain, $context, $original);

		if (isset($translation[$key]) && $translation[$key] !== '') {
			return $translation[$key];
		}

		return ($key === 1) ? $original : $plural;
	}

	public static function isPlural ($n) {
		return ($n === 1) ? 1 : 2;
	}
}
