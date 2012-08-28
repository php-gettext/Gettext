<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Json {
	static public function generate (Entries $entries) {
		$domain = $entries->getDomain() ?: 'messages';

		$json = array(
			'' => array(
				'domain' => $domain,
				'lang' => 'en',
				'plural-forms' => 'nplurals=2; plural=(n != 1);'
			)
		);

		$context_glue = '\\u0004';

		foreach ($entries as $translation) {
			$entry = array($translation->getPlural(), $translation->getTranslation());

			if ($translation->hasPluralTranslation()) {
				$entry = array_merge($entry, $translation->getPluralTranslation());
			}

			if ($translation->hasContext()) {
				$json[$translation->getContext().$context_glue.$translation->getOriginal()] = $entry;
			} else {
				$json[$translation->getOriginal()] = $entry;
			}
		}

		return json_encode(array($domain => $json));
	}
}
