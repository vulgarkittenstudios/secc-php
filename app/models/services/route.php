<?php
/**
* This class is used to bootstrap the routing
* system for the application. URLs are are
* parsed and controllers are handled here
*/

namespace secc\models\services;
use secc\app;
class route
{
	protected static $instance 	= null; // Used to store an instance of this object
	protected static $routes 	= []; // Stores all of the routes registered for the application 
	protected static $params	= []; // Stores the params passed to a route in the url
	protected static $url 		= []; // Stores the actual url passed through the web browser

	private static $routesFile 	= '../app/routes.json'; // Stores the path to the routes.json file

	private static $path; // Stores the path of a specific route
	private static $name; // Stores the name of a specific route
	private static $controller; // Stores the controller of a specific route
	private static $method; // Stores the method on the controller of a specific route
	private static $filters; // Stores an array of the filters that restrict a specific route

	/**
	 * Set class wide variables and get properties for a specific route if specified
	 * 
	 * @param String $name the route that you wish to recieve properties for specifically
	 */
	public function __construct($params = null)
	{
		// Set the proper file path to the routes.json based on where the file is being called from 
		if($_SERVER['PHP_SELF'] === 'lace')
			self::$routesFile = 'app/routes.json';
		else self::$routesFile = '../app/routes.json';

		self::getUrl(); // Parse the url passed through the browser
		self::$routes = self::parseRoutesFile(self::$routesFile); // Parse the app/routes.json file

		// Check if we need to set properties for a specific route
		if(isset($params))
		{
			// Loop the routes
			for($i = 0; $i < count(self::$routes); $i++)
			{
				// Check if the route is the one specified
				if($params === self::$routes[$i]->name)
				{
					// Set individual properties that corrospond to the route in the app/routes.json file
					self::$path = (self::$routes[$i]->path === '/') ? config::get('server.base_url').self::$routes[$i]->path : config::get('server.base_url').'/'.self::$routes[$i]->path;
					self::$name = self::$routes[$i]->name;
					self::$controller = self::$routes[$i]->controller;
					self::$method = self::$routes[$i]->method;
					self::$filters = (isset(self::$routes[$i]->filters)) ? self::$routes[$i]->filters : [];
				}
			}
		}
	}

	################################
	## BEGIN GETTERS AND SETTERS ##
	###############################
	/**
	 * Getter for the path of a specific route
	 * 
	 * @return String the path of the route
	 */
	public static function path() { return self::$path; }

	/**
	 * Getter for the name of a specific route
	 * 
	 * @return String the name of the route
	 */
	public static function name() { return self::$name; }

	/**
	 * Getter for the controller of a specific route
	 * 
	 * @return String the controller of the route
	 */
	public static function controller() { return self::$controller; }

	/**
	 * Getter for the method on a controller for a specific route
	 * 
	 * @return String the method on the controller of the route
	 */
	public static function method() { return self::$method; }

	/**
	 * Getter for the filters that restrict a specific route
	 * 
	 * @return Array the filters that restrict the route
	 */
	public static function filters() { return self::$filters; }
	##############################
	## END GETTERS AND SETTERS ##
	#############################

	/**
	 * Implement a singleton pattern for this class
	 * 
	 * @return class
	 */
	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
		// return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}
	
	/**
	 * This method ties all the different parts of the
	 * application's routes together in the correct order.
	 * 
	 * @return [type] [description]
	 */
	public static function initialize()
	{
		$check = []; // Used to check if the url matches a route
		for($i = 0; $i < count(self::$routes); $i++)
		{
			$route = (object)(self::$routes[$i]);
			
			// If the url matches a rout pass a true value to the $check array
			if(self::$url[0] == $route->path || self::$url[0] == $route->path.'/')
				array_push($check, 'true');

			if(!empty($check)) // If there are any values in the $check array, execute the route
			{
				// Get the current route
				$route = (object)(self::$routes[$i]);
				self::setUrlParams($i);

				if(isset($route->filters))
					return self::filterRoutes($i);
				else
					return self::callRouteController($i);
			}
		}

		// If the $check array is empty redirect to a 404 error
		if(empty($check)) return redirect::to(404);
	}

	/**
	 * This method takes reads the routes.json file
	 * and turns it into an array, it also digs
	 * into the the object structure and 
	 * returns the proper array data.
	 * 
	 * @param  String $file the path to the routes.json file
	 * @return  $array the parsed routes.json file
	 */
	public static function parseRoutesFile($file)
	{
		$routes = json_decode(file_get_contents($file));
		$array 	= [];
		foreach($routes as $key)
			foreach($key as $route)
				$array[] = $route;

		return $array;
	}

	/**
	 * All This function does is get the url if it exists
	 */
	private static function getUrl()
	{
		(input::get('url')) ? self::$url[] = input::get('url') : self::$url[] = '/';
	}

	/**
	 * Extract the params from the route's path
	 * and replace them with the params that
	 * have been passed in through the url
	 * 
	 * @param int $index the itteration index of the loop through the routes
	 */
	public static function setUrlParams($index)
	{
		$route = (object)(self::$routes[$index]); // Convert route array into an object
		// If params exist push them into the variable or if not use an empty array
		self::$params = preg_match_all('/{(.*?)}/', $route->path, $params) ? array_values($params[1]) : [];

		if(!empty(self::$params))
		{
			// Reverse the url and the route's path with params
			$url 			= explode('/', input::get('url'));
			$url 			= array_reverse($url);
			self::$params 	= array_reverse(self::$params);

			// Loop through all of the params in a routes path
			for($i = 0; $i < count(self::$params); $i++)
			{
				if(isset($url[$i]))
					self::$params[$i] = $url[$i]; // Replace the param in the rout's path with the url param
			}

			// Flip the url and params arrays back around
			self::$params 	= array_reverse(self::$params);
			$url 			= array_reverse(self::$params);

			$full_path 		= []; // Store the actual path

			// merge the url and the params arrays together and esure no values are duplicated
			$full_path 		= array_unique(array_merge($url_array, self::$params));
			$full_path 		= implode('/', $full_path); // create the url string from arrays

			$route->path 	= $full_path; // set the path of the route with param values
		}
	}

	/**
	 * This method checks if any filters have been
	 * attached to the current route, and applies
	 * each one if they exist.
	 * 
	 * @param  int $index the itteration index of the loop through the routes
	 * @return If the route succeeds, return the controller, else return 404
	 */
	public static function filterRoutes($index)
	{
		// Get the current route
		$route = (object)(self::$routes[$index]);
		$check = []; // A container to hold all passed filters

		// Apply all filters specified in the route
		for($i = 0; $i < count($route->filters); $i++)
		{
			$route->filters[$i] = App::filter($route->filters[$i]);

			if($route->filters[$i]->main())
				array_push($check, $route->filters[$i]);

			// Call the controller with params if the 
			if(count($check) === count($route->filters))
				self::callRouteController($index);
			else
				return redirect::to(401);
		}
	}

	/**
	 * This method simply calls the controller
	 * class and method that is attached to 
	 * the current route in the itteration
	 * 
	 * @param  int $index the itteration index of the loop through the routes
	 * @return class |The controller & method attached to the route|
	 */
	public static function callRouteController($index)
	{
		// Get the current route
		$route = (object)(self::$routes[$index]);
		if(count(self::$params))
			return call_user_func_array([app::controller($route->controller), $route->method], self::$params);
		else
			return app::controller($route->controller)->{$route->method}();
	}
}