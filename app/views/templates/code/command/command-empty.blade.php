<?= '<?php' ?>

/**
* 
*/

namespace secc\commands;

use secc\app;

class {{ $name }}
{
	// Used to store the instance of this object
	private static $instance = null;

	// Path to the target directory
	protected static $directory = "";

	/**
	 *	This method is used to return an instance of this object
	 * 
	 * @return class
	 */
	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	/**
	 *	This method is used to display
	 *	information about this command
	 * 
	 * @return string
	 */
	public static function description()
	{
		Shell::write(" description", "white").Shell::write(" - ").Shell::write("Description Here. \n", "dark_grey");
	}

	/**
	 *	This method is used to display helpful usage
	 *	information about this command to the terminal.
	 * 
	 * @return string
	 */
	public static function help($method = null)
	{
		LACE::header(); // Display the LACE header image
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

	/**
	 *	This function's sole purpose is to
	 *	display help information for 
	 *	this functin's make method
	 * 
	 * @return string
	 */
	public static function makeHelp()
	{
		Shell::write(" make", "light_green").Shell::write("        - ").Shell::write("Description Here. \n", "light_red");
		Shell::write("   usage", "light_blue").Shell::write("     - ").Shell::write("make:", "yellow").Shell::write("name\n", "purple");
	}

	/**
	 * Description Here
	 * 
	 * @param  string
	 * @return void
	 */
	public static function make($name = '', $type = 'empty')
	{
		return self::makeHelp();
		$file = trim(self::$directory.$name.'.php');
		if(file_exists($file))
			return Shell::write("The file $file already exists. \n", "red");

		switch($type)
		{
			case 'empty':
				$template = App::view()->make('templates.code{{ $name }}.{{ $name }}-empty', ['name' => $name]);

				if(file_put_contents($file, $template))
					Shell::write("File successfully created! \n", "light_green");
				else
					Shell::write("An error occured while creating the file!", "red");
			break;
		}
	}

	/**
	 *	This function's sole purpose is to
	 *	display help information for 
	 *	this functin's delete method
	 * 
	 * @return string
	 */
	public static function deleteHelp()
	{
		Shell::write(" delete", "light_green").Shell::write("      - ").Shell::write("Description Here. \n", "light_red");
		Shell::write("   usage", "light_blue").Shell::write("     - ").Shell::write("delete:", "yellow").Shell::write("name\n", "purple");
	}

	/**
	 * Description Here	
	 *
	 * @param  string
	 * @return void
	 */
	public static function delete($name = null)
	{
		return self::deleteHelp();
		$file = trim(self::$directory.$name.'.php');
		if(file_exists($file))
		{
			if(unlink($file))
				Shell::write("The file was successfully deleted! \n", "light_green");
			else
				Shell::write("An error occured while deleting the file! \n", "red");
		}
		else
		{
			Shell::write("The specified file could not be found. \n", "red");
		}
	}
}
