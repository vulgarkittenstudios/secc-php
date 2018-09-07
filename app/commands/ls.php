<?php
/**
* 
*/

namespace secc\commands;

use secc\app;

class ls
{
	// Used to store the instance of this object
	private static $instance = null;

	// Path to the target directory
	protected static $directory = "";

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
		shell::write(" description", "white").shell::write(" - ").shell::write("This command is useful for finding various components in the framework. \n", "dark_grey");
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
				case "commands": self::commandsHelp(); break;
				case "routes": self::routesHelp(); break; 
			}
		}
		else // Display help information for all available methods in this command
		{
			self::commandsHelp();
			self::routesHelp();
		}
	}

	public static function commandsHelp()
	{
		shell::write(" commands", "light_green").shell::write("    - ").shell::write("Lists all available LACE commands. \n", "light_red");
	}

	public static function commands()
	{
		lace::header();
		$commands = scandir('app/commands');
		for($i = 0; $i < count($commands); $i++)
		{
			// Exclude files that cannot be called from the terminal and add only valid LACE commands
			if($commands[$i] != '.' && $commands[$i] != '..' && $commands[$i] != 'LACE.php' && $commands[$i] != 'Shell.php')
			{
				$command = explode('.', $commands[$i]);
				shell::write($command[0]." ", "light_green");
				app::command($command[0])->description();
			}
		}
	}

	public static function routesHelp()
	{
		shell::write(" routes", "light_green").Shell::write("      - ").shell::write("Lists all available LACE commands. \n", "light_red");
		shell::write("   usage", "light_blue").Shell::write("     - ").shell::write("Leave params blank to list all routes \n	       or enter the listing method you want to use. \n", "light_red");
		shell::write("           ").shell::write("    routes:", "yellow").shell::write("all", "purple").shell::write(" - Lists all data contained in a route. \n", "light_grey");
		shell::write("           ").shell::write("    routes:", "yellow").shell::write("filtered", "purple").shell::write(" - Only lists routes that have filters. \n", "light_grey");
	}

	/**
	 * Display information about routes
	 * 
	 * @param  string $type how to display routes
	 */
	public static function routes($type = "empty")
	{
		$routes = app::route()->parseRoutesFile("app/routes.json");
		switch ($type) 
		{
			case 'empty':

				for($i = 0; $i < count($routes); $i++)
					shell::write($routes[$i]->name."\n", "purple");
			break;

			case 'all':
				for($i = 0; $i < count($routes); $i++)
				{
					shell::write($routes[$i]->name."\n", "purple");
					shell::write("    path: ", "white").shell::write($routes[$i]->path."\n", "light_grey");
					shell::write("    controller: ", "white").shell::write($routes[$i]->controller."\n", "light_grey");
					shell::write("    method: ", "white").shell::write($routes[$i]->method."\n", "light_grey");
					if(isset($routes[$i]->filters))
					{
						shell::write("    filters: ", "white").shell::write("[", "light_grey");
						$index = 1;
						for($x = 0; $x < count($routes[$i]->filters); $x++)
						{
							if($index === count($routes[$i]->filters))
								shell::write($routes[$i]->filters[$x], "light_grey");
							else if($index < count($routes[$i]->filters))
								shell::write($routes[$i]->filters[$x].", ", "light_grey");
							else shell::write($routes[$i]->filters[$x], "light_grey");
							$index++;
						}
						shell::write("]\n", "light_grey");
					}
				}
			break;

			case 'filtered':
				for($i = 0; $i < count($routes); $i++)
					if(isset($routes[$i]->filters))
						shell::write($routes[$i]->name."\n", "purple");
			break;
		}
	}
}
