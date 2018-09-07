<?php
/**
* 
*/

namespace secc\commands;

use secc\app;

class controller
{
	// Used to store the instance of this object
	private static $instance = null;

	// Path to the target directory
	protected static $directory = "app/controllers/";

	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	public static function description()
	{
		shell::write(" description", "white").shell::write(" - ").shell::write("This command is used to manage controller classes. \n", "dark_grey");
	}

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
				case 'delete': self::deleteHelp(); break;
			}
		}
		else // Display help information for all available methods in this command
		{
			self::makeHelp();
			self::deleteHelp();
		}
	}

	public static function makeHelp()
	{
		shell::write(" make", "light_green").shell::write("        - ").shell::write("Used to generate a controller class. \n", "light_red");
		shell::write("   usage", "light_blue").shell::write("     - ").shell::write("make:", "yellow").shell::write("name\n", "purple");
	}

	public static function make($name = '', $type = 'empty')
	{
		if(!$name) return self::makeHelp();
		$file = trim(self::$directory.$name.'.php');
		if(file_exists($file))
			return shell::write("The file $file already exists. \n", "red");

		switch($type)
		{
			case 'empty':
				$template = app::view()->make('templates.code.controller.controller-empty', ['name' => $name]);

				if(file_put_contents($file, $template))
					shell::write("File successfully created! \n", "light_green");
				else
				{
					error_reporting(E_ALL & ~E_WARNING);
					return shell::write("An error occured while creating the file.. make sure the directory exists! \n", "red");
				}
			break;
		}

		// Dump the autoload file or output a warning telling the user to do it
		if(!shell::composer("dump-autoload -o"))
			return shell::write("Could not generate autoload file! \nYou will need to do it manually...\n", "yellow");
	}

	public static function deleteHelp()
	{
		shell::write(" delete", "light_green").shell::write("      - ").shell::write("Used to delete a controller class. \n", "light_red");
		shell::write("   usage", "light_blue").shell::write("     - ").shell::write("delete:", "yellow").shell::write("name\n", "purple");
	}

	public static function delete($name = null)
	{
		if(!$name) return self::deleteHelp();
		$file = trim(self::$directory.$name.'.php');
		if(file_exists($file))
		{
			if(unlink($file))
				shell::write("The file was successfully deleted! \n", "light_green");
			else
				return shell::write("An error occured while deleting the file! \n", "red");
		}
		else
		{
			return shell::write("The specified file could not be found. \n", "red");
		}

		// Dump the autoload file or output a warning telling the user to do it
		if(!shell::composer("dump-autoload -o"))
			return shell::write("Could not generate autoload file! \nYou will need to do it manually...\n", "yellow");
	}
}
