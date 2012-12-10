<?php
namespace Gettext\Extractors;

use Gettext\Entries;

abstract class Extractor {
	static public function extract ($file, Entries $entries = null) {
		if (empty($file)) {
			throw new \InvalidArgumentException('There is not a file defined');
			return false;
		}

		if ($entries === null) {
			$entries = new Entries;
		}

		if (($file = self::resolve($file)) === false) {
			return false;
		}

		if (is_array($file)) {
			foreach ($file as $f) {
				static::extract($f, $entries);
			}

			return $entries;
		}

		if (!is_readable($file)) {
			throw new \InvalidArgumentException("'$file' is not a readable file");
			return false;
		}

		static::parse($file, $entries);

		return $entries;
	}

	static private function resolve ($path) {
		if (is_string($path)) {
			if (is_file($path)) {
				return $path;
			}

			if (is_dir($path)) {
				$files = array();

				$directory = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
				$iterator = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::LEAVES_ONLY);

				foreach ($iterator as $fileinfo) {
					$name = $fileinfo->getPathname();

					if (strpos($name, '/.') === false) {
						$files[] = $name;
					}
				}

				return $files;
			}

			throw new \InvalidArgumentException("'$path' is not a valid file or folder");
			return false;
		}

		if (is_array($path)) {
			$files = array();

			foreach ($path as $file) {
				$file = static::resolve($file);

				if (is_array($file)) {
					$files = array_merge($files, $file);
				} else {
					$files[] = $file;
				}
			}

			return $files;
		}

		throw new \InvalidArgumentException('The first argumet must be string or array');
		return false;
	}
}
