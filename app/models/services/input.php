<?php
/**
* This class is used as a wrapper arround 
* the PHP input type super globals and
* handle escaping strings from forms
*
* |GLOBALS AVAILABLE|
* $_GET, 
* $_POST,
* $_FILES,
* $_REQUEST
*/

namespace secc\models\services;
use secc\app;
class input
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
	
	/**
	 * Check if input exists
	 * 
	 * @param  string $type the type of input to check for
	 * @return bool
	 */
	public static function exists($type = 'post')
	{
		switch($type)
		{
			case 'get': return (!empty($_GET)) ? true : false; break;
			case 'post': return (!empty($_POST)) ? true : false; break;
			case 'files': return (!empty($_FILES)) ? true : false; break;
			case 'request': return (!empty($_REQUEST)) ? true : false; break;
		}
	}

	/**
	 * Convert specified input type to json object
	 * 
	 * @param  String $type the type of input superglobal array to convert
	 * @param  String $escaped escape slashes
	 * @return json
	 */
	public static function all($type = 'post', $escaped = true)
	{
		switch($type)
		{
			case 'get': return (!empty($_GET)) ? ($escaped) ? json_encode($_GET) : json_encode($_GET, JSON_UNESCAPED_SLASHES) : null; break;
			case 'post': return (!empty($_POST)) ? ($escaped) ? json_encode($_POST) : json_encode($_POST, JSON_UNESCAPED_SLASHES) : null; break;
			case 'files': return (!empty($_FILES)) ? ($escaped) ? json_encode($_FILES) : json_encode($_FILES, JSON_UNESCAPED_SLASHES) : null; break;
			case 'request': return (!empty($_REQUEST)) ? ($escaped) ? json_encode($_REQUEST) : json_encode($_REQUEST, JSON_UNESCAPED_SLASHES) : null; break;
		}
	}

	/**
	 * Access the $_GET superglobal with specified key
	 * 
	 * @param  String $name $_GET[$name]
	 * @return $_GET[$name]
	 */
	public static function get($name)
	{
		return (isset($_GET[$name])) ? $_GET[$name] : null;
	}

	/**
	 * Access the $_POST superglobal with specified key
	 * 
	 * @param  String $name $_POST[$name]
	 * @return $_POST[$name]
	 */
	public static function post($name)
	{
		return (isset($_POST[$name])) ? $_POST[$name] : null;
	}

	/**
	 * Access the $_FILES superglobal with specified key
	 * 
	 * @param  string $name $_FILES[$name]
	 * @return $_FILES[$name]
	 */
	public static function files($name)
	{
		return (isset($_FILES[$name])) ? $_FILES[$name] : null;
	}

	/**
	 * Access the $_REQUEST superglobal with specified key
	 * 
	 * @param  string $name $_REQUEST[$name]
	 * @return $_REQUEST[$name]
	 */
	public static function request($name)
	{
		return (isset($_REQUEST[$name])) ? $_REQUEST[$name] : null;
	}

	/**
	 * Escape the specified string
	 * 
	 * @param  String $string the string to escape
	 * @return String
	 */
	public static function escape($string)
	{
		return htmlentities($string, ENT_QUOTES, 'UTF-8');
	}
}