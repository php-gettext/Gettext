<?php
namespace Gettext\Extractors;

use Gettext\Entries;

class PhpCode extends Extractor {
	static public $functions = array(
		'__' => '__',
		'__e' => '__',
		'n__' => 'n__',
		'n__e' => 'n__',
		'p__' => 'p__',
		'p__e' => 'p__'
	);

	static public function parse ($file, Entries $entries) {
		$tokens = token_get_all(file_get_contents($file));
		$functions = array();
		$currentFunction = null;

		foreach ($tokens as $k => $value) {
			if (is_string($value)) {
				if ($value === ')' && $currentFunction) {
					$functions[] = $currentFunction;
					$currentFunction = null;
				}

				continue;
			}

			if ($currentFunction && ($value[0] === T_CONSTANT_ENCAPSED_STRING)) {
				$val = $value[1];

				if ($val[0] === '"') {
					$val = str_replace('\\"', '"', $val);
				} else {
					$val = str_replace("\\'", "'", $val);
				}

				$currentFunction[] = substr($val, 1, -1);
				continue;
			}

			if (!$currentFunction && ($value[0] === T_STRING) && is_string($tokens[$k + 1]) && ($tokens[$k + 1] === '(')) {
				$currentFunction = array($value[1], $value[2]);
				continue;
			}
		}

		foreach ($functions as $args) {
			$function = array_shift($args);

			if (!isset(self::$functions[$function])) {
				continue;
			}

			$line = array_shift($args);

			switch (self::$functions[$function]) {
				case '__':
					$original = $args[0];
					$translation = $entries->find('', $original) ?: $entries->insert('', $original);
					break;

				case 'n__':
					$original = $args[0];
					$plural = $args[1];
					$translation = $entries->find('', $original, $plural) ?: $entries->insert('', $original, $plural);
					break;

				case 'p__':
					$context = $args[0];
					$original = $args[1];
					$translation = $entries->find($context, $original) ?: $entries->insert($context, $original);
					break;
			}

			$translation->addReference($file, $line);
		}
	}
}
