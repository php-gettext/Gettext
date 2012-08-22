<?php
namespace Gettext;

class Translator {
	static private $dictionary = array();
	static private $context = '';

	public static function loadTranslations ($file) {
		if (is_file($file)) {
			$dictionary = include($file);

			if (is_array($dictionary)) {
				self::addTranslations($dictionary);
			}
		}
	}

	public static function addTranslations (array $dictionary) {
		self::$dictionary = array_replace(self::$dictionary, $dictionary);
	}

	public static function clearTranslations () {
		self::$dictionary = array();
	}

	public static function hasTranslation ($original) {
		return (isset(self::$dictionary[$original]) && self::$dictionary[$original] !== '') ? true : false;
	}

	public static function gettext ($original) {
		if (self::hasTranslation($original)) {
			return self::$dictionary[$original];
		}

		return $original;
	}

	public static function ngettext ($original, $plural, $n) {
		if (static::isPlural($n)) {
			$original = $plural;
		}

		return gettext($original);
	}

	public static function isPlural ($n) {
		return ($n == 1) ? false : true;
	}
}
