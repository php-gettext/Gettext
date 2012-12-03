<?php
namespace Gettext\Generators;

use Gettext\Entries;
use Gettext\Generators\Php;

class Jed extends Generator {
	static public function generate (Entries $entries, $encoded = true) {
		$translations = Php::generate($entries);
		
		return $encoded ? json_encode($translations) : $translations;
	}
}
