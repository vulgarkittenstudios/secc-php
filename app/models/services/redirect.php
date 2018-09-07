<?php
/**
*
*/

namespace secc\models\services;
use secc\app;
class redirect
{
	// Stores a reference to this class
	protected static $instance = null;

	public function __construct($params = null)
	{
		if(isset($params))
			return self::route($params);
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
	
	public static function to($location = null)
	{
		if($location)
		{
			if(is_numeric($location))
			{
				switch($location)
				{
					case 401:
					header('HTTP/1.1 401 Unauthorized');
					view::render('errors.401');
					exit();
					break;

					case 404:
					header('HTTP/1.0 404 Not Found');
					view::render('errors.404');
					exit();
					break;
				}
			}

			header('Location: '.$location);
			exit();
		}
	}

	public static function route($string = '')
	{
		return app::route($string)->path();
	}
}