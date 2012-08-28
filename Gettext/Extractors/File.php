<?php
namespace Gettext\Extractors;

use Gettext\Entries;

class File extends Extractor {
	static public function parse ($file, Entries $entries) {
		$lines = file($file, FILE_IGNORE_NEW_LINES);

		foreach ($lines as $num => $text) {
			preg_match_all('/__e?\(((?<!\\\)"(.*?)(?<!\\\)"|(?<!\\\)\'(.*?)(?<!\\\)\')/i', $text, $matches);

			if (!isset($matches[1])) {
				continue;
			}

			foreach ($matches[3] as $msgid) {
				$msgid = str_replace('\\', '', $msgid);

				$translation = $entries->find(null, $msgid) ?: $entries->append(null, $msgid);
				$translation->addReference($file, $num + 1);
			}
		}
	}
}
