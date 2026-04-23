<?php
spl_autoload_register(function (string $class): void {
	require(str_replace('\\', '/', $class) . '.php');
});