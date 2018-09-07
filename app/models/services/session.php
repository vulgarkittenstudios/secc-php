<?php
/**
* This class is used to wrap the $_SESSION
* superglobal so that it's easier to manage
* values and use them throughout the app.
*/

namespace secc\models\services;
use secc\app;
class session
{
	protected static $instance = null;

	public function __construct($params = null)
	{
		if(isset($params))
			return self::get($params);
	}

	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
	}
	
	/**
	 * Checks if a key exists in the $_SESSION array
	 * 
	 * @param  String $name The key to search for
	 * @return Bool         Feedback as to whether it exists
	 */
	public static function exists($name)
	{
		return (isset($_SESSION[$name])) ? true : false;
	}

	/**
	 * This method creates a new key/value pair in
	 * the $_SESSION superglobal array.
	 * 
	 * @param  String $name  The name of the key
	 * @param  String $value The value to give the key
	 * @return String        Just returns after setting the session key/value pair
	 */
	public static function put($name, $value)
	{
		return $_SESSION[$name] = $value;
	}

	/**
	 * This method is used to retrieve a key/value pair
	 * from the $_SESSION superglobal array.
	 * 
	 * @param  String $name The name of the key to search for
	 * @return String       The value of the session key
	 */
	public static function get($name)
	{
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
	}

	/**
	 * This method is used to delete a key/value pair
	 * from the $_SESSION superglobal array.
	 * 
	 * @param  String $name The name of the key to delete
	 */
	public static function delete($name)
	{
		if(self::exists($name))
		{
			unset($_SESSION[$name]);
		}
	}

	/**
	 * This method is used to flash a message to the
	 * user with the $_SESSION functionality
	 * 
	 * @param  String $name   The name of the key to use
	 * @param  String $string The value of the session key
	 * @return String         The session key value if exists
	 */
	public static function flash($name, $string = '')
	{
		if(self::exists($name))
		{
			$session = self::get($name);
			self::delete($name);
			return $session;
		}
		else
		{
			self::put($name, $string);
		}
	}
}