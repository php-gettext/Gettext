<?php
namespace Fol;

class Loader {
	static private $libraries_path;
	static private $classes = array();
	static private $namespaces = array();


	/**
	 * static public function setLibrariesPath (string $libraries_path)
	 *
	 * Sets the base path for load the libraries
	 */
	static public function setLibrariesPath ($libraries_path) {
		if (is_dir($libraries_path)) {
			self::$libraries_path = $libraries_path;
		} else {
			throw new \ErrorException("The folder '$libraries_path' does not exists");
		}
	}



	/**
	 * static public function register ()
	 *
	 * Installs this class loader on the SPL autoload stack.
	 */
	static public function register () {
		spl_autoload_register(__NAMESPACE__.'\\Loader::autoload');
	}


	/**
	 * static public function unregister ()
	 *
	 * Uninstalls this class loader from the SPL autoloader stack.
	 */
	static public function unregister () {
		spl_autoload_unregister(__NAMESPACE__.'\\Loader::autoload');
	}



	/**
	 * static public function autoload ($class_name)
	 *
	 * Basic autoload function
	 * Returns boolean
	 */
	static public function autoload ($class_name) {
		$file = self::getFile($class_name);

		if ($file && is_readable($file)) {
			include_once($file);
		}
	}



	/**
	 * static public function getFile ($class_name)
	 *
	 * Find a class file
	 * Returns string/false
	 */
	static public function getFile ($class_name) {
		$class_name = ltrim($class_name, '\\');

		if (isset(self::$classes[$class_name])) {
			return self::$classes[$class_name];
		}

		$namespace = '';

		if (($last_pos = strripos($class_name, '\\')) !== false) {
			$namespace = substr($class_name, 0, $last_pos);
			$class_name = substr($class_name, $last_pos + 1);
		}

		foreach (self::$namespaces as $ns => $path) {
			if (strpos($namespace, $ns) === 0) {
				return self::filePath(preg_replace('#^'.$ns.'#', '', $namespace), $class_name, $path);
			}
		}

		return self::filePath($namespace, $class_name);
	}



	/**
	 * static private function filePath (string $namespace, string $class_name, [array $options])
	 *
	 * Generate the filename
	 * Returns string/boolean
	 */
	static private function filePath ($namespace, $class_name, $libraries_path = null) {
		$file = isset($libraries_path) ? $libraries_path : self::$libraries_path;

		if (!empty($namespace)) {
			$file .= '/'.str_replace('\\', '/', $namespace);
		}

		return $file.'/'.str_replace('_', '/', $class_name).'.php';
	}



	/**
	 * static public function registerClass (array $classes)
	 * static public function registerClass (string $class, string $path)
	 *
	 * Sets a new path for an specific class
	 * Returns none
	 */
	static public function registerClass ($class, $path = null) {
		if (is_array($class)) {
			foreach ($class as $class => $path) {
				self::$classes[$class] = $path;
			}

			return;
		}

		self::$classes[$class] = $path;
	}



	/**
	 * static public function registerNamespace (array $namespaces)
	 * static public function registerNamespace (string $namespace, string $path)
	 *
	 * Sets a new base path for an specific namespace
	 * Returns none
	 */
	static public function registerNamespace ($namespace, $path = null) {
		if (is_array($namespace)) {
			foreach ($namespace as $namespace => $path) {
				self::$namespaces[$namespace] = $path;
			}

			return;
		}

		self::$namespaces[$namespace] = $path;
	}



	/**
	 * static public function registerComposer ()
	 *
	 * Register the classes installed by composer
	 * Returns none
	 */
	static function registerComposer () {
		$file = self::$libraries_path.'composer/autoload_classmap.php';

		if (is_file($file)) {
			self::registerClass(include($file));
		}

		$file = self::$libraries_path.'composer/autoload_namespaces.php';

		if (is_file($file)) {
			foreach (include($file) as $namespace => $path) {
				self::registerNamespace($namespace, $path.$namespace.'/');
			}
		}
	}
}
?>
