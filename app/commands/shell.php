<?php

/**
* This class's sole purpose
* is to output the desired
* text to the terminal.
*/

namespace secc\commands;
use secc\app;
class shell
{
	private static $input;

	private static $text_colors 		= [];
	private static $background_colors 	= [];

	private static function initialize()
	{
		// Set up shell colors
		self::$text_colors['black'] = '0;30';
		self::$text_colors['dark_grey'] = '1;30';
		self::$text_colors['blue'] = '0;34';
		self::$text_colors['light_blue'] = '1;34';
		self::$text_colors['green'] = '0;32';
		self::$text_colors['light_green'] = '1;32';
		self::$text_colors['cyan'] = '0;36';
		self::$text_colors['light_cyan'] = '1;36';
		self::$text_colors['red'] = '0;31';
		self::$text_colors['light_red'] = '1;31';
		self::$text_colors['purple'] = '0;35';		
		self::$text_colors['light_purple'] = '1;35';
		self::$text_colors['brown'] = '0;33';
		self::$text_colors['yellow'] = '1;33';
		self::$text_colors['light_grey'] = '0;37';
		self::$text_colors['white'] = '1;37';

		// Set up background colors
		self::$background_colors['black'] = '40';
		self::$background_colors['red'] = '41';
		self::$background_colors['green'] = '42';
		self::$background_colors['yellow'] = '43';
		self::$background_colors['blue'] = '44';
		self::$background_colors['magenta'] = '45';
		self::$background_colors['cyan'] = '46';
		self::$background_colors['light_grey'] = '47';
	}

	public static function write($string, $forground_color = null, $background_color = null)
	{
		self::initialize();
		$colored_string = "";

		// Check if the forground color exists
		if(isset(self::$text_colors[$forground_color]))
		{
			$colored_string .= "\033[" . self::$text_colors[$forground_color] . "m";
		}

		// Check if the background color exists
		if(isset(self::$background_colors[$background_color]))
		{
			$colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .= $string . "\033[0m";
		echo $colored_string;
	}
	
	public static function input()
	{
		return self::$input = trim(fgets(STDIN, 1024));
	}

	/**
	 * Determines if a command exists on the current environment
	 *
	 * @param string $command The command to check
	 * @return bool True if the command has been found ; otherwise, false.
	 */
	public static function where ($command) {
	  $whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';

		$process = proc_open(
			"$whereIsCommand $command",
			[
				0 => array("pipe", "r"), //STDIN
				1 => array("pipe", "w"), //STDOUT
				2 => array("pipe", "w"), //STDERR
			],
			$pipes
		);
		if ($process !== false) 
		{
			$stdout = stream_get_contents($pipes[1]);
			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			proc_close($process);

			return $stdout != '';
		}

		return false;
	}

	/**
	 * Executes a shell command and return feedback
	 * 
	 * @param  String  $cmd    the command to exectute
	 * @param  boolean $output the output of the executed command
	 * @return String|bool     Either returns a string of output or bool feedback
	 */
	public static function command($cmd, $output = false)
	{
		exec($cmd, $output, $return);

		return ($return !== 0) ? ($output) ? $output : false : ($output) ? $output : true ;
	}

	/**
	 * Shortcut to the composer command
	 * 
	 * @param  String $cmd the composer command to execute
	 * @return String|bool       feedback if the command executed successfully
	 */
	public static function composer($cmd)
	{
		// Tell the script where composer is and if 
		if(self::where("composer"))
			$composer = "composer ";
		else if(app::config("composer.path") !== "" || app::config("composer.path") !== null)
			$composer = app::config("composer.path")." ";
		else self::write("composer could not be found! \nInstall it, or specify the path to the \ncomposer.phar file in the environment.json file.", "light_red");
		
		return self::command($composer.$cmd);
	}
}
