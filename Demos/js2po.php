<?php
function autoload ($className) {
	$className = ltrim($className, '\\');
	$fileName  = dirname(__DIR__).DIRECTORY_SEPARATOR;
	$namespace = '';
	
	if ($lastNsPos = strripos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}

	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if (is_file($fileName)) {
		require $fileName;
	}
}

function buildOptions ($args) {
	$options = array(
		'-o' => null,
		'-i' => null
	);

	$len = count($args);
	$i = 0;

	while ($i < $len) {
		if (preg_match('#^-[a-z]$#i', $args[$i])) {
			$options[$args[$i]] = isset($args[$i+1]) ? trim($args[$i+1]) : true;
			$i += 2;
		} else {
			$options[] = $args[$i];
			$i++;
		}
	}
	return $options;
}

spl_autoload_register('autoload');

$options = buildOptions($argv);
$input = $options['-i'];
$output = $options['-o'];

//Custom functions
Gettext\Extractors\JsCode::$functions = array(
	'T_' => '__'
);

if (!strpos($input, ':\\') && $input[0] !== DIRECTORY_SEPARATOR) {
	$input = __DIR__.DIRECTORY_SEPARATOR.$input;
}
if (!strpos($output, ':\\') && $output[0] !== DIRECTORY_SEPARATOR) {
	$output = __DIR__.DIRECTORY_SEPARATOR.$output;
}

$Entries = Gettext\Extractors\JsCode::extract($input);
Gettext\Generators\Po::generateFile($Entries, $output);
