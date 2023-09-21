<?php
	
	function classLoader($class)
	{
		$class = strtolower($class);
		$path = '../class/'.$class.'.php';
		if(file_exists($path)) require_once($path);
		return false;
	}

	spl_autoload_register('classLoader');

?>