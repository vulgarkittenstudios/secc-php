<?php

/**
* This class serves as a container for the entire application.
* All initializations should be done here in the initialize
* function. Any lower level systems should be called here.
*/

namespace secc;

class app
{
	/**
	* This is the bootstrap function that ties everything together
	* so execution order of any calls made here matters. Route 
	* initialization should be the last thing called here.
	 */
	public static function initialize()
	{
		// Start our browser session
		session_cache_limiter(false);
		session_start();

		// Load in the whoops pretty page handler
		self::service('errorHandler')->initialize();

		// Load the database if credentials exist in the configuration file
		if(self::config('db.name') != '' && self::config('db.username') != '' && self::config('db.password') != '')
			self::service('database')->initialize();

		// !!EXECUTE LAST!!
		self::route()->initialize();
	}

	/**
	 * Get's an asset file from the public url
	 * (Requires server.base_url to be set in conf)
	 * @param  String $string the path to the asset
	 * @return String         the full url path to the asset
	 */
	public static function asset($string = '')
	{
		return self::config('server.base_url').'/'.$string;
	}

	/**
	 *	This method will return an accessor class from app/models/accessors
	 * 
	 * @param  string
	 * @return class
	 */
	public static function accessor($class, $params = null)
	{
		$accessor = 'secc\\models\\accessors\\'.$class;
		if(class_exists($accessor))
			return (isset($params)) ? $accessor::instance($params) : $accessor::instance();
	}

	/**
	 *	This method will return a filter class from app/models/services
	 * 
	 * @param  string
	 * @return class
	 */
	public static function filter($class, $params = null)
	{
		$filter = 'secc\\models\\filters\\'.$class;
		if(class_exists($filter))
			return (isset($params)) ? $filter::instance($params) : $filter::instance();
	}


	/**
	 *	This method will return a service class from app/models/services
	 * 
	 * @param  string
	 * @return class
	 */
	public static function service($class, $params = null)
	{
		$service = 'secc\\models\\services\\'.$class;
		if(class_exists($service))
			return (isset($params)) ? $service::instance($params) :  $service::instance();
	}


	/**
	 *	This method will return a command class from app/commands
	 * 
	 * @param  string
	 * @return class
	 */
	public static function command($class, $params = null)
	{
		$command = 'secc\\commands\\'.$class;
		if(class_exists($command))
			return (isset($params)) ? $command::instance($params) : $command::instance();
	}

	/**
	 *	This method will return a controller class from app/models/services
	 * 
	 * @param  string
	 * @return class
	 */
	public static function controller($class, $params = null)
	{
		$controller = 'secc\\controllers\\'.$class;
		if(class_exists($controller))
			return (isset($params)) ? $controller::instance($params) : $controller::instance();
	}

	/**
	 *	This method will return a command class from app/commands
	 *	(Shortcut for App::command)
	 * 
	 * @param  string
	 * @return class
	 */
	public static function cmd($cmd, $params = null)
	{
		return (isset($params)) ? self::command($cmd, $params) : self::command($cmd);
	}

	/**
	 * Getter for the Auth service. Returns the user if 
	 * specified in the params.
	 * 
	 * @param  String $params The user that we want to get data for
	 * @return class          The auth class
	 */
	public static function auth($params = null)
	{
		return (isset($params)) ? self::service('auth', $params) : self::service('auth');
	}

	/**
	 *	Getter that directly uses the Config service to return 
	 *	configuration strings from the environment.json file.
	 * 
	 * @param  string
	 * @return string
	 */
	public static function config($string) 
	{ 
		return self::service('config')->get($string); 
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Database'
	 * 
	 * @return class
	 */
	public static function database($params = null)
	{
		return (isset($params)) ? self::service('database', $params) : self::service('database');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Hash'
	 * 
	 * @return class
	 */
	public static function hash($params = null)
	{
		return (isset($params)) ? self::service('hash', $params) : self::service('hash');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Input'
	 * 
	 * @return class
	 */
	public static function input($params = null)
	{
		return (isset($params)) ? self::service('input', $params) : self::service('input');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Mailer'
	 * 
	 * @return class
	 */
	public static function mailer($params = null)
	{
		return (isset($params)) ? self::service('mailer', $params) : self::service('mailer');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Redirect'
	 * 
	 * @return class
	 */
	public static function redirect($params = null)
	{
		return (isset($params)) ? self::service('redirect', $params) : self::service('redirect');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Route'
	 * 
	 * @return class
	 */
	public static function route($params = null)
	{
		return (isset($params)) ? self::service('route', $params) : self::service('route');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Session'
	 * 
	 * @return class
	 */
	public static function session($params = null)
	{
		return (isset($params)) ? self::service('session', $params) : self::service('session');
	}

	/**
	 * Getter / Setter for the Token class
	 * 
	 * @param  String $params Checks a token if it's set, otherwise it generates one
	 * @return String/Bool
	 */
	public static function token($params = null)
	{
		return (isset($params)) ? self::service("token")->check($params) : self::service('token')->generate();
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'Utils'
	 * 
	 * @return class
	 */
	public static function utils($params = null)
	{
		return (isset($params)) ? self::service('utils', $params) : self::service('utils');
	}

	/**
	 *	This method is a shortcut used to directly return 
	 *	an instance of the service class 'View'
	 * 
	 * @return class
	 */
	public static function view($params = null)
	{ 
		return (isset($params)) ? self::service('view', $params) : self::service('view'); 
	}
}
