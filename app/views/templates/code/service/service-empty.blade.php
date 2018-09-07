<?= '<?php' ?>

/**
*
*/

namespace secc\models\services;
use secc\app;
class {{$name}}
{
	protected static $instance = null;

	public function __construct($params = null)
	{
		
	}

	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
	}
	
	public static function initialize()
	{
		
	}
}