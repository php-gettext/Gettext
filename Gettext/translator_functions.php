<?php
use Gettext\Translator;

function __ ($original) {
	$text = Translator::gettext($original);

	if (func_num_args() === 1) {
		return $text;
	}

	$args = array_slice(func_get_args(), 1);

	return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __n ($original, $plural, $value) {
	$text = Translator::ngettext($original, $plural, $value);

	if (func_num_args() === 3) {
		return $text;
	}

	$args = array_slice(func_get_args(), 3);

	return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __p ($context, $original) {
	$text = Translator::pgettext($context, $original);

	if (func_num_args() === 2) {
		return $text;
	}

	$args = array_slice(func_get_args(), 2);

	return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __d ($domain, $original) {
	$text = Translator::dgettext($domain, $original);

	if (func_num_args() === 2) {
		return $text;
	}

	$args = array_slice(func_get_args(), 2);

	return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __dp ($domain, $context, $original) {
	$text = Translator::dpgettext($domain, $context, $original);

	if (func_num_args() === 3) {
		return $text;
	}

	$args = array_slice(func_get_args(), 3);

	return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __dnp ($domain, $context, $original, $plural, $value) {
	$text = Translator::dnpgettext($domain, $context, $original, $plural, $value);

	if (func_num_args() === 5) {
		return $text;
	}

	$args = array_slice(func_get_args(), 5);

	return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}


function __e ($original) {
	echo (func_num_args() === 1) ? __($original) : __($original, array_slice(func_get_args(), 1));
}
