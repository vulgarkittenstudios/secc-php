<?php

/**
* 
*/

namespace secc\models\services;

use duncan3dc\Laravel\BladeInstance;

class view
{
	protected static $blade;

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

	private static function bladeInstance($views = '../app/views', $cache = '../app/views/cache')
	{
		self::$blade = new BladeInstance($views, $cache);
	}

	public static function render($view, $data = null)
	{
		if($_SERVER['PHP_SELF'] == 'lace')
			self::bladeInstance('app/views');
		else
			self::bladeInstance('../app/views');

		if(!isset($data))
		{
			echo self::$blade->render($view);
		}
		else
		{
			echo self::$blade->render($view, $data);
		}
	}

	public static function make($view, $data = null)
	{
		if($_SERVER['PHP_SELF'] == 'lace')
			self::bladeInstance('app/views');
		else
			self::bladeInstance('../app/views');

		if(!isset($data))
		{
			return self::$blade->make($view);
		}
		else
		{
			return self::$blade->make($view, $data);
		}
	}
}
