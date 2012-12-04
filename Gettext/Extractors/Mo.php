<?php
namespace Gettext\Extractors;

use Gettext\Entries;
use Gettext\Translation;

class Mo extends Extractor {
	public $error = 0;

	const MAGIC1 = -1794895138;
	const MAGIC2 = -569244523;
	const MAGIC3 = 2500072158;

	static public function parse ($file, Entries $entries) {
		$stream = new CachedFileReader($file);

		if (!$stream || isset($stream->error)) {
			return false;
		}

		$magic = self::readInt($stream, 'V');

		if (($magic === self::MAGIC1) || ($magic === self::MAGIC3)) { //to make sure it works for 64-bit platforms
			$byteOrder = 'V'; //low endian
		} elseif ($magic === (self::MAGIC2 & 0xFFFFFFFF)) {
			$byteOrder = 'N'; //big endian
		} else {
			$this->error = 1; //not MO file

			return false;
		}

		self::readInt($stream, $byteOrder);

		$total = self::readInt($stream, $byteOrder); //total string count
		$originals = self::readInt($stream, $byteOrder); //offset of original table
		$translations = self::readInt($stream, $byteOrder); //offset of translation table

		$stream->seekto($originals);
		$table_originals = self::readIntArray($stream, $byteOrder, $total * 2);
		$stream->seekto($translations);
		$table_translations = self::readIntArray($stream, $byteOrder, $total * 2);

		for ($i = 0; $i < $total; $i++) {
			$stream->seekto($table_originals[$i * 2 + 2]);
			$original = $stream->read($table_originals[$i * 2 + 1]);

			if ($original) {
				$stream->seekto($table_translations[$i * 2 + 2]);
				$entries->insert(null, $original)->setTranslation($stream->read($table_translations[$i * 2 + 1]));
			}
		}
	}

	static private function readInt ($stream, $byteOrder) {
		if (($read = $stream->read(4)) === false) {
			return false;
		}

		$read = unpack($byteOrder, $read);

		return array_shift($read);
	}

	static private function readIntArray ($stream, $byteOrder, $count) {
		return unpack($byteOrder.$count, $stream->read(4 * $count));
	}
}


class CachedFileReader {
	public $pos;
	public $str;
	public $strlen;

	public function __construct ($filename) {
		if (is_file($filename)) {
			$length = filesize($filename);
			$fd = fopen($filename,'rb');

			if (!$fd) {
				$this->error = 3; // Cannot read file, probably permissions

				return false;
			}

			$this->str = fread($fd, $length);
			$this->strlen = strlen($this->str);

			fclose($fd);
		} else {
			$this->error = 2; // File doesn't exist

			return false;
		}
	}

	public function read ($bytes) {
		$data = substr($this->str, $this->pos, $bytes);

		$this->seekto($this->pos + $bytes);

		return $data;
	}

	public function seekto ($pos) {
		$this->pos = ($this->strlen < $pos) ? $this->strlen : $pos;

		return $this->pos;
	}
}
