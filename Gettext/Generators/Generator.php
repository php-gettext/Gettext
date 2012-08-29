<?php
namespace Gettext\Generators;

use Gettext\Entries;

abstract class Generator {
	static public function generateFile (Entries $entries, $file) {
		$content = static::generate($entries, true);

		if (file_put_contents($file, $content) === false) {
			return false;
		}

		return true;
	}
}
