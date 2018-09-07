<?php

/**
* This class is the base container for the LACE
* cli system. It scans the commands directory
* for valid command classes executing them.
*/

namespace secc\commands;

use secc\app;

class lace
{
	// Stores valid lace commands
	protected static $commands = [];

	/**
	 * @param  int[] count of arguments passed through the command line
	 * @param  string[] array of actual commands passed to the cli
	 */
	public static function initialize($argc = null, $argv = null)
	{
		
		// If the only input is lace it'self or help, output the header and info about LACE
		if($argc === 1 || $argv[1] === 'help')
		{
			self::header(); // Out put the header at the top of the terminal
			shell::write("description", "light_grey").shell::write(" - ").shell::write("LACE is used to manage components of your application. \n", "light_grey");
			shell::write("usage", "light_green").shell::write("       - ").shell::write("php lace [command] ", "yellow").shell::write("[method]", "purple").shell::write(":", "light_red").shell::write("[params] \n\n", "light_blue");
			shell::write("Get additional information by passing ", "light_grey").shell::write("help", "purple").shell::write(" as the method for a command. \n", "light_grey");
			shell::write("List available commands with the ", "light_grey").shell::write("ls", "yellow").shell::write(" ").shell::write("commands ", "purple").shell::write("command. \n", "light_grey");
		}

		self::setValidCommands(); // Set the valid commands before we start parsing the input
		if(isset($argv[1]))
		{
			// Check if the command is valid
			if(!in_array($argv[1], self::$commands))
			{
				// Output an error if the command cannot be found
				shell::write("You need to enter a valid LACE command! \n", "red");
			}
			else 
			{
				// Store the command
				$command = app::command($argv[1]);

				// Check if a method has been passed in with a command
				if($argc > 2)
				{
					$methods = []; // Container to store the methods

					// Loop through all of the methods that have been passed to a command
					for($i = 0; $i < $argc - 2; $i++)
					{
						// Grab the methods and exclude the command it's self
						$methods[] = $argv[$i + 2];

						// Check if params have been passed to the method
						if(strpos($methods[$i], ':'))
						{
							// Extract the arguments from the method
							$argument = $methods[$i];
							$argument = explode(':', $argument);

							$method = $argument[0]; // Isolate the method from the arguments string
							unset($argument[0]); // Remove the method from the arguments string
							$params = $argument; // Store the params

							// Check if params are passed to the method
							if(count($params) > 0)
							{
								// Error check and call the method with params
								if(method_exists($command, $method))
									call_user_func_array([$command, $method], $params);
								else
									shell::write("Warnning! That is not a valid method for the command '$argv[1]' \n", 'yellow');
							}
						}
						else
						{
							// Error check and call the method without params
							if(method_exists($command, $methods[$i]))
								$command->{$methods[$i]}();
							else
								shell::write("Warnning! That is not a valid method for the command '$argv[1]' \n", 'yellow');
						}
					}
				}
				else
				{
					// Call the command with the help method if no method has been passed to the command
					$command->help();
				}
			}
		}
	}

	/**
	 * This function scans the app/commands directory and interperates
	 * each file to see if it is a valid LACE command or not, then 
	 * stores it in an array of usable commands for execution.
	 */
	private static function setValidCommands()
	{
		$commands = scandir('app/commands');
		for($i = 0; $i < count($commands); $i++)
		{
			// Exclude files that cannot be called from the terminal and add only valid LACE commands
			if($commands[$i] != '.' && $commands[$i] != '..' && $commands[$i] != 'lace.php' && $commands[$i] != 'shell.php')
			{
				$commands[$i] = explode('.', $commands[$i]);
				self::$commands[] = $commands[$i][0];
			}
		}
	}

	/**
	 *	This method retrieves the view that is the LACE header
	 *	gives it a random color and outputs it to the terminal.
	 * 
	 * @return void
	 */
	public static function header()
	{
		// Retrieve the header view.
		$header = app::view()->make('templates.code.header');

		// Store all possible colors that we can give the header.
		$colors = [

			"red",
			"light_red",
			"yellow",
			"blue",
			"green",
			"light_blue",
			"cyan",
			"purple",
			"light_purple"
		];

		// Echo the header with a random color
		shell::write($header."\n", $colors[rand(0, count($colors) - 1)]);
	}
}
