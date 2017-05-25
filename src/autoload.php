<?php
function passy_autoload($class)
{
	$path = realpath(__DIR__) . '/' . str_replace('\\', '/', $class) . '.php';
	if (is_readable($path))
		require_once $path;
}

spl_autoload_register('passy_autoload');
