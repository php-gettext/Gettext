<?php
namespace Gettext\Generators;

use Gettext\Entries;
use Gettext\Generators\PhpArray;

class Jed extends Generator {
	static public function generate (Entries $entries, $encoded = true) {
		$translations = PhpArray::generate($entries);
		
		return $encoded ? json_encode($translations) : $translations;
	}
}
