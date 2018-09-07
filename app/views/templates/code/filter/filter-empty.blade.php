<?= '<?php' ?>

/**
* 
*/

namespace secc\models\filters;
use secc\app;
class {{$name}}
{
	
	private static $instance = null;

	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	public static function main()
	{
		return true;
	}
}
