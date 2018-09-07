<?= '<?php' ?>

/*
*
*/

namespace secc\controllers;
use secc\app;
class {{ $name }}
{
	public static $instance = null;

	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	public static function index()
	{
		
	}
}
