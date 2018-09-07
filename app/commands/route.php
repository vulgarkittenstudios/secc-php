<?php

/**
* This class is a LACE command that is used 
* to manage the app/routes.json file. Here
* you can add, delete, edit etc... routes
*/

namespace secc\commands;

use secc\app;

class route
{
	// Used to store the instance of this object
	private static $instance 	= null;
	private static $routes 		= [];
	private static $properties 	= [];

	/**
	 *	This method is used to return an instance of this object
	 * 
	 * @return  class
	 */
	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	/**
	 *	This method is used to display
	 *	information about this command
	 * 
	 * @return  string
	 */
	public static function description()
	{
		shell::write(" description", "white").shell::write(" - ").shell::write("This command is used to manage your routes.json file. \n", "dark_grey");
	}

	/**
	 *	This method is used to display helpful usage
	 *	information about this command to the terminal.
	 * 
	 * @return  string
	 */
	public static function help($method = null)
	{
		lace::header(); // Display the LACE header image
		self::description(); // Display the description for this command

		// Check if a method has been specified to obtain information for
		if(isset($method))
		{
			// Describe the specified method
			switch($method)
			{
				case 'make': self::makeHelp(); break;
				case 'edit': self::editHelp(); break;
				case 'delete': self::deleteHelp(); break;
			}
		}
		else // Display help information for all available methods in this command
		{
			self::makeHelp();
			self::editHelp();
			self::deleteHelp();
		}
	}

	/**
	 *	This function's sole purpose is to
	 *	display help information for 
	 *	this functin's make method
	 * 
	 * @return  string
	 */
	public static function makeHelp()
	{
		shell::write(" make", "light_green").shell::write("        - ").shell::write("Enter the name of the route you wish to create. \n", "light_red");
		shell::write("   usage", "light_blue").shell::write("     - ").shell::write("make:", "yellow").shell::write("name\n", "purple");
	}

	/**
	 * This method parses the routes.json file
	 * and creates a new route according to 
	 * the type of route that is specified.
	 * 
	 * @param    string
	 * @return  void
	 */
	public static function make($name = '', $type = 'empty')
	{
		if(!$name) return self::makeHelp();

		// Get the routes from the route.json file and put them in an array
		self::$routes = app::route()->parseRoutesFile('app/routes.json');

		$run = true; // Used to start the input reception process
		// Check if the route already exists and exit with error if so
		for($i = 0; $i < count(self::$routes); $i++)
		{
			if($name === self::$routes[$i]->name)
			{
				shell::write("That route already exists! \n", "red");
				$run = false;
			}
		}
		
		while($run) // Start input loop
		{	
			lace::header();
			switch($type)
			{
				case 'empty': // Execute for default 'empty' route type

					// Specify the fields to input into the route
					$fields = ["path", "controller", "method"];
					$route 	= self::constructRoute($name, $fields); // Construct the route

					if(!empty($route))
					{
						self::$routes[] = $route; // Add the new route to the routes array
						self::$routes 	= self::formatRoutesObject(); // Format the routes array
						if(self::save(self::$routes)) // Save the routes array to app/routes.json
						{
							shell::write("Route createed successfully! \n", "light_green");
							$run = false;
						}
						else
						{
							shell::write("An error occured while saving this route! \n", "red");
							$run = false;
						}
					}
				break;

				case 'filtered': // Execute for 'filtered' route type
					// Specify the fields to input into the route
					$fields = ["path", "controller", "method", "filters"];
				break;
			}
		}	
	}

	public static function edit($name = '', $type = 'empty', $fields = '')
	{
		if(!$name) return self::editHelp();

		// Get the routes from the route.json file and put them in an array
		self::$routes = App::route()->parseRoutesFile('app/routes.json');

		for($i = 0; $i < count(self::$routes); $i++)
			if($name === self::$routes[$i]->name)
				$route = self::$routes[$i];

		if(isset($route))
		{
			LACE::header();
			$run = true;
			while($run)
			{
				switch($type)
				{
					case 'empty':
						
						$fields = ["name", "path", "controller", "method"];
						for($i = 0; $i < count($fields); $i++)
						{
							Shell::write("Enter the ".$fields[$i]." that you would like to give this route. \n", "light_blue");
							Shell::write(">>  ", "yellow");

							$route->{$fields[$i]} = Shell::input();
						}

						if(self::update($name, $route))
							return Shell::write("Successfully updated route! \n", "light_green");
						else return Shell::write("There was an error updating the route! \n", "red");

					break;

					case 'fields':
						// Check if multiple fields have been specified
						if(strpos($fields, '/'))
						{
							$fields = explode('/', $fields); // Create an array out of fields

							// Check if filters has been spcified as part of the edited fields
							if(in_array("filters", $fields))
							{
								Shell::write("Enter the filters that you would like to attach to this route \nseperated by a '/' if there are more than one. \n", "light_blue");
								Shell::write(">>  ", "yellow");

								$filters = Shell::input(); // Capture the input

								// Setup an array of filters to attach to this route
								if(strpos($filters, '/'))
									$filters = explode('/', $filters);
								else $filters = [$filters];

								// Set the filters to the constructed array
								$route->filters = $filters;
							}

							for($i = 0; $i < count($fields); $i++)
							{
								if($fields[$i] === "filters")
									continue;

								Shell::write("Enter the ".$fields[$i]." that you would like to give this route. \n", "light_blue");
								Shell::write(">>  ", "yellow");

								$route->{$fields[$i]} = Shell::input();
							}

							if(self::update($name, $route))
								return Shell::write("Successfully updated route! \n", "light_green");
							else return Shell::write("There was an error updating the route! \n", "red");
						}

						if($fields === "filters")
						{
							Shell::write("Enter the filters that you would like to attach to this route \nseperated by a '/' if there are more than one. \n", "light_blue");
							Shell::write(">>  ", "yellow");

							$filters = Shell::input(); // Capture the input

							// Setup an array of filters to attach to this route
							if(strpos($filters, '/'))
								$filters = explode('/', $filters);
							else $filters = [$filters];

							// Set the filters to the constructed array
							$route->filters = $filters;

							// Save the filters to the route
							if(self::update($name, $route))
								return Shell::write("Successfully updated route! \n", "light_green");
							else return Shell::write("There was an error updating the route! \n", "red");
						}

						Shell::write("Enter the ".$fields." that you would like to give this route. \n", "light_blue");
						Shell::write(">>  ", "yellow");

						$route->$fields = Shell::input(); // Capture the input

						// Save the routes with the new field
						if(self::update($name, $route))
							return Shell::write("Successfully updated route! \n", "light_green");
						else return Shell::write("There was an error updating the route! \n", "red");
					break;
				}
			}
		}

		// Throw an error if no route was found
		return Shell::write("That route could not be found! \n", "red");
	}

	public static function editHelp()
	{
		Shell::write(" edit", "light_green").Shell::write("        - ").Shell::write("Enter the name of the route that you wish to edit. \n", "light_red");
		Shell::write("   usage", "light_blue").Shell::write("     - ").Shell::write("edit:", "yellow").Shell::write("name\n", "purple");
		Shell::write("        ", "light_blue").Shell::write("     - ").Shell::write("edit:", "yellow").Shell::write("name:", "purple").Shell::write("fields:", "green").Shell::write("fields/to/edit\n", "light_grey");
	}

	/**
	 *	This function's sole purpose is to
	 *	display help information for 
	 *	this functin's delete method
	 * 
	 * @return  string
	 */
	public static function deleteHelp()
	{
		Shell::write(" delete", "light_green").Shell::write("      - ").Shell::write("Enter the name of the route that you wish to delete. \n", "light_red");
		Shell::write("   usage", "light_blue").Shell::write("     - ").Shell::write("delete:", "yellow").Shell::write("name\n", "purple");
	}

	/**
	 * This method parses the routes.json file
	 * and searches for a named route. If it's
	 * found, attempt to delete it from the file.
	 *
	 * @param   String name of the route you wish to delete
	 * @return  String notification
	 */
	public static function delete($name = null)
	{
		if(!$name) return self::makeHelp();

		// Get the routes from the route.json file and put them in an array
		self::$routes = App::route()->parseRoutesFile('app/routes.json');

		for($i = 0; $i < count(self::$routes); $i++) // Loop through all routes
		{
			// Check if the name of the route is the same as the name passed to this method
			if(self::$routes[$i]->name === $name)
			{
				$run = true;
				while($run) // Envoke a question to ensure that the user wants to delete the route
				{
					// Draw the question
					Shell::write("Are you sure you want to delete this route?", "yellow").Shell::write("(yes/no) \n", "light_red");
					Shell::write(">>  ", "light_green");

					// Check for positive input
					if(Shell::input() === "yes")
					{
						unset(self::$routes[$i]); // Remove the route from the array
						self::$routes = self::formatRoutesObject(self::$routes); // Format the array
						if(self::save(self::$routes)) // Save the array to the routes.json file
							return Shell::write("Route successfully deleted! \n", "light_green");
						else
							return Shell::write("There was a problem deleting the route! \n", "red");
					}

					// Notify the user that the question has recieved a negitive response
					Shell::write("Bye! \n", "light_blue");
					return $run = false;
				}
			}
			
		}

		// Notify the user that there is no route with that name
		return Shell::write("That route could not be found! \n", "red");
	}

	/**
	 * This method checks the fields specified by the type
	 * of route the user specifies, captures the input
	 * from the shell and pushes it to the route array
	 * 
	 * @param  String $name   the name of the route
	 * @param  Array $fields the fields to add to the route
	 * @return Array         the final result of user input
	 */
	private static function constructRoute($name, $fields)
	{
		$properties 	= [];
		$route 			= [];
		//die(var_dump($route));
		foreach($fields as $key => $field)
		{
			Shell::write("Enter the name of the ".$field." that you would like to attach to this route. \n", "light_blue");
			Shell::write(">>  ", "yellow");

			$properties[] = Shell::input();

			if($key == count($fields)-1)
			{
				$route 			= array_combine($fields, $properties);
				$route["name"] 	= $name;
				return $route;
			}
		}
	}

	/**
	 * This method takes all of the routes
	 * and formats them into a structure
	 * that the system can read
	 * 
	 * @return Array properly formatted routes
	 */
	private static function formatRoutesObject()
	{
		$routes = []; // Final version of the routes array
		foreach(self::$routes as $key => $route)
		{
			$routes["routes"][$key] = $route;
		}
		return $routes;
	}

	/**
	 * This method saves the routes.json file
	 * 
	 * @param  Array $obj This is the routes array that should be saved as json
	 * @return Bool      Give feedback as to whether or not saving is successful.
	 */
	private static function save($obj)
	{
		if(!empty(self::$routes))
		{
			if(file_put_contents('app/routes.json', json_encode($obj, JSON_UNESCAPED_SLASHES)))
				return true;
			else return false;
		}
		else return false;
	}

	public static function update($name, $route)
	{
		for($i = 0; $i < count(self::$routes); $i++)
			if($name === self::$routes[$i]->name)
				self::$routes[$i] = $route;

		self::$routes = self::formatRoutesObject();
		if(self::save(self::$routes))
			return true;
		else return false;
	}
}
