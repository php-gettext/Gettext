<?php
namespace Gettext\Generators;

use Gettext\Entries;

class PhpArray extends Generator {
	static public function generate (Entries $entries, $string = false) {
		$array = array();

		$context_glue = '\u0004';

		foreach ($entries as $translation) {
			$key = ($translation->hasContext() ? $translation->getContext().$context_glue : '').$translation->getOriginal();
			$entry = array($translation->getPlural(), $translation->getTranslation());

			if ($translation->hasPluralTranslation()) {
				$entry = array_merge($entry, $translation->getPluralTranslation());
			}

			$array[$key] = $entry;
		}

		$domain = $entries->getDomain() ?: 'messages';

		$translations = array(
			$domain => array(
				'' => array(
					'domain' => $domain,
					'lang' => 'en',
					'plural-forms' => 'nplurals=2; plural=(n != 1);'
				)
			)
		);

		$translations[$domain] = array_merge($translations[$domain], $array);

		if ($string) {
			return '<?php return '.var_export($translations, true).'; ?>';
		}

		return $translations;
	}
}
