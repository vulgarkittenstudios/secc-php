<?php
/**
*
*/

namespace secc\models\services;

class config
{
	// Stores a reference to this class
	protected static $instance = null;
	
	
	public function __construct($params = null)
	{

	}

	/**
	 * Implement a singleton pattern for this class
	 * 
	 * @return class
	 */
	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
	}
	
	public static function get($name)
	{
		if($_SERVER['PHP_SELF'] == 'lace')
			$file = file_get_contents('config.json');
		else
			$file = file_get_contents('../config.json');

		$name = explode('.', $name);
		$file = json_decode($file);
		return $file->{$name[0]}->{$name[1]};
	}
}