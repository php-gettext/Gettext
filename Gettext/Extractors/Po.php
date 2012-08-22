<?php
namespace Gettext\Extractors;

use Gettext\Entries;
use Gettext\Translation;

class Po extends Extractor {
	static public function parse ($file, Entries $entries) {
		$lines = file($file, FILE_IGNORE_NEW_LINES);

		$i = 2;

		while (($line = trim($lines[$i++])) !== '') {
			$line = self::clean($line);

			if (strpos($line, ':')) {
				$header = explode(':', $line, 2);
				$entries->setHeader($header[0], $header[1]);
			}
		}

		$translation = new Translation;

		for ($n = count($lines); $i < $n; $i++) {
			$line = trim($lines[$i]);

			if ($line === '' && $translation->hasOriginal()) {
				$entries[] = $translation;

				$translation = new Translation;
				continue;
			}

			list($key, $data) = preg_split('/\s/', $line, 2);
			$append = null;

			switch ($key) {
				case '#,':
				case '#':
				case '#.':
					$translation->addComment($data);
					$append = null;
					break;

				case '#:':
					if (strpos($data, ':')) {
						$data = explode(':', $data);
						$translation->addReference($data[0], $data[1]);
					}
					$append = null;
					break;

				case 'msgctxt':
					$translation->setContext(self::clean($data));
					$append = 'Context';
					break;

				case 'msgid':
					$translation->setOriginal(self::clean($data));
					$append = 'Original';
					break;

				case 'msgid_plural':
					$translation->setPlural(self::clean($data));
					$append = 'Plural';
					break;

				case 'msgstr':
				case 'msgstr[0]':
					$translation->setTranslation(self::clean($data));
					$append = 'Translation';
					break;

				case 'msgstr[1]':
					$translation->setPluralTranslation(self::clean($data));
					$append = 'PluralTranslation';
					break;

				default:
					if (strpos($key, 'msgstr[') === 0) {
						$translation->addPluralTranslation(self::clean($data));
						$append = 'PluralTranslation';
						break;
					}

					if (isset($append)) {
						if ($append === 'PluralTranslation') {
							$key = count($this->getPluralTranslation()) - 1;
							$this->setPluralTranslation($this->getPluralTranslation($key).self::clean("\n".$data), $key);
							break;
						}

						$getMethod = 'get'.$append;
						$setMethod = 'set'.$append;
						$translation->$setMethod($translation->$getMethod().self::clean("\n".$data));
					}
					break;
			}
		}

		return $entries;
	}

	static private function clean ($str) {
		if ($str[0] === '"') {
			$str = substr($str, 1, -1);
		}

		return str_replace(array('\\n', '\\"'), array("\n", '"'), $str);
	}
}