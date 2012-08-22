<?php
namespace Gettext\Extractors;

use Gettext\Entries;

abstract class Extractor {
	static public function extract ($file, Entries $entries = null) {
		if ($entries === null) {
			$entries = new Entries;
		}

		if (is_array($file)) {
			foreach ($file as $f) {
				static::parse($f, $entries);
			}

			return $entries;
		}

		static::parse($file, $entries);

		return $entries;
	}
}
