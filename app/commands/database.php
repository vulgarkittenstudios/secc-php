<?php

/**
* This command is used to manage your database.
* Migrations, backups, and table managment,
* can be accomplished with this command.
*/

namespace secc\commands;

use secc\app;

class database
{
	// Used to store the instance of this object
	private static $instance = null;

	// Path to the target directory
	protected static $directory = "app/database/";
	protected static $db;

	/** Ensure that an instance of the database is called when executing this command */
	public function __construct()
	{
		app::database()->initialize(); // Initialize a database connection
		self::$db = app::database(); // Get the database class
	}

	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	public static function description()
	{
		shell::write(" description", "white").shell::write(" - ").shell::write("This command is used to manage your database. \n", "dark_grey");
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
				case "migrate": self::migrateHelp();
				case "backup": self::backupHelp();
				case "restore": self::restoreHelp();
			}
		}
		else // Display help information for all available methods in this command
		{
			self::migrateHelp();
			self::backupHelp();
			self::restoreHelp();
		}
	}

	public static function migrateHelp()
	{

	}

	public static function migrate($type = null, $name = null, $params = null)
	{
		switch ($type) 
		{
			case 'make':
			return (isset($params)) ? self::migrateMake($name, $params) : self::migrateMake($name);
			break;

			case 'delete':
			return (isset($params)) ? self::migrateDelete($name, $params) : self::migrateDelete($name);
			break;

			case 'up':
			return self::operation($name, "up", $params);
			break;

			case 'down':
			return self::operation($name, "down", $params);

			case 'rollback':
			return self::operation($name, "rollback", $params);
			break;
		}
	}

	public static function backupHelp()
	{

	}

	public static function backup($tables = null, $driver = 'mysql')
	{
		$date = date("Y m d H:i:s");
		$date = str_replace(' ', '_', $date);
		$date = str_replace(':', '.', $date);

		$driver = app::config('db.driver');

		switch($driver)
		{
			case 'mysql':
				if(isset($tables))
				{
					$tables = explode('.', $tables);
					
					for($i = 0; $i < count($tables); $i++)
					{
						if(!file_exists(self::$directory."backups/".$tables[$i]))
							app::utils()->mkdir(self::$directory."backups/".$tables[$i]);

						$command = "mysqldump --opt -h" 
						. app::config('db.host') ." -u" 
						. app::config('db.username') ." -p" 
						. app::config('db.password') ." " 
						. app::config('db.name') . " " . $tables[$i] . " > " 
						. self::$directory."backups/".$tables[$i]."/"
						. $tables[$i]."_".app::config('db.name')."_".$date.'.sql';

						if(shell::command($command))
							shell::write("The database table ".$tables[$i]." has been backed up! \n", "light_green");
						else
							shell::write("Something went wrong backing up the database table".$tables[$i]."! \n", "red");
					}
				}
				else
				{
					$command = "mysqldump --opt -h" 
					. app::config('db.host') ." -u" 
					. app::config('db.username') ." -p" 
					. app::config('db.password') ." " 
					. app::config('db.name') ." > " 
					. self::$directory."backups/"
					. app::config('db.name')."_".$date.'.sql';

					if(shell::command($command))
						shell::write("The database has been backed up! \n", "light_green");
					else
						shell::write("Something went wrong backing up the database! \n", "red");
				}
			break;
		}
	}


	public static function restoreHelp()
	{

	}

	public static function restore($tables = null, $driver = 'mysql')
	{
		switch ($driver) 
		{
			case "mysql":

				
			break;
		}
	}

	private static function properties($string)
	{
		$properties = (object)([]); // A container to hold migration properties

		// Split the string with either dots or slashes and put it in an array
		$string = preg_split("/\.|\//", $string);

		// Extract the file name
		$properties->file = array_reverse($string);
		$properties->file = ($properties->file[0] === "php") ? $properties->file[1].".php" : $properties->file[0];

		// Extract the file path
		$properties->path = array_reverse($string);
		if($properties->path[0] === "php") 
			unset($properties->path[0], $properties->path[1]);
		else unset($properties->path[0]);

		$properties->path = array_reverse($properties->path);
		$properties->path = implode("/", $properties->path);

		// Extract the class name
		$properties->name = (strpos($properties->file, ".")) ? explode('.', $properties->file) : $properties->file;
		if(is_array($properties->name))
			$properties->name = $properties->name[0];

		// Extract the table name
		if(strpos($properties->name, "_"))
		{
			$properties->table = explode("_", $properties->name);
			$properties->table = $properties->table[0];
		}

		// Extract the class version
		$properties->version = (strpos($properties->name, "_")) ? explode("_", $properties->file) : $properties->file;
		if(is_array($properties->version))
			$properties->version = $properties->version[1];

		$properties->version = intval($properties->version);


		// Extract the class namespace
		$properties->namespace = str_replace(self::$directory."migrations/", "secc\\database\\migrations\\", $properties->path);
		$properties->namespace = str_replace("/", "\\", $properties->namespace);

		return $properties;
	}

	private static function migrations($table = null)
	{
		// Use recursive directory iteration to get all the currently existing migration files
		$migrations = app::utils()->scandirRecursive(self::$directory."migrations");

		$records = []; // Stores the migrations
		foreach($migrations as $migration)
		{
			$mig = self::properties($migration);

			if(!isset($table))
				$records[] = self::properties($migration); // Get all migration properties

			if($mig->table === $table)
				$records[] = self::properties($migration); // Get migration properties of 1 table name
		}
		return $records;
	}

	private static function migrateMake($name = null, $type = 'empty')
	{
		if(!$name)
			return shell::write("You need to specify a name for your migration! \n", "light_red");

		// Get the path passed to this function and give it psuedo migration properties
		$properties = self::properties(self::$directory."migrations/".$name);
		// Get the migration files with their properties
		$migrations = self::migrations();

		// Create the file path if it doesn't exist
		if(!file_exists($properties->path))
			app::utils()->mkdir($properties->path);

		$properties->version = 1; // Initialize the version to 1
		for($i = 0; $i < count($migrations); $i++)
		{
			$migration = (object)($migrations[$i]);
			
			// If the version is less than previous version then increase the version to 1 more than that.
			if($migration->path === $properties->path)
				$properties->version = ($properties->version + 1);
		}

		// Check what type of template to use for the migration
		switch($type)
		{
			case "empty":
				// Set the template to the 'empty' type and pass it relivent data
				$template = app::view()->make('templates.code.database.migration-empty', ['properties' => $properties]);
			break;
		}

		// Save the file and output a success message or fail and output error message
		if(file_put_contents($properties->path."/".$properties->name."_".$properties->version.".php", $template))
			shell::write($properties->name." successfully created! \n", "light_green");
		else
			shell::write($properties->name." failed to save! \n", "red");

		shell::composer("dump-autoload -o");
	}

	private static function migrateDelete($name, $type = 'empty')
	{
		if(isset($name))
		{
			// Get the path passed to this function and give it psuedo migration properties
			$properties = self::properties(self::$directory."migrations/".$name);
			
			switch ($type) 
			{
				case 'empty':
				$migrations = self::migrations($properties->name);

				for($i = 0; $i < count($migrations); $i++)
				{
					$migration = (object)($migrations[$i]);
					if(unlink($migration->path."/".$migration->file))
						shell::write("Successfully deleted migration table file ".$migration->file."! \n", "light_green");
					else shell::write("An error occurred while deleting migration table file ".$migration->file."! \n", "red");					
				}

				if(rmdir($properties->path."/".$properties->name))
					shell::write("Successfully deleted table directory for ".$properties->name."! \n", "light_green");
				else shell::write("An error occurred while deletinog migration table directory for ".$properties->name."! \n", "red");
				break;
			}
		}
		else return shell::write("You must specify a migration to delete! \n", "red");

		shell::composer("dump-autoload -o");
	}

	private static function operation($name = null, $type = 'up', $v = null)
	{
		// Get the path passed to this function and give it psuedo migration properties
		$properties = self::properties(self::$directory."migrations/".$name);
		// Get the migration files with their properties
		$migrations = self::migrations();

		$version 	= 1;
		$subjects 	= [];
		for($i = 0; $i < count($migrations); $i++)
		{
			$migration = (object)($migrations[$i]);

			if($name === null || is_numeric($name))
			{
				if(is_numeric($name))
					$version = $name;

				if(!is_numeric($name) && $version < $migration->version)
					$version = $migration->version;
				
				if($version === $migration->version)
					$subjects[$migration->table] = $migration;
			}
			else
			{
				if(!isset($v) && $version < $migration->version)
					$version = $migration->version;
				
				if(isset($v))
					$version = $v;

				if($migration->table === $properties->name)
					$subjects[$migration->table] = $migration;
			}
		}

		$subjects = array_unique($subjects, SORT_REGULAR);
		foreach($subjects as $subject)
		{
			if($v)
			{
				$subject->version = $v;

				$mig = $subject->namespace."\\".$subject->table."_".$subject->version;
			}
			else
				$mig = $subject->namespace."\\".$subject->name;  
    
    $m = explode('\\', $mig);
    $m[0] = 'secc';
    $mig = implode($m, '\\');
    
    
			$mig = new $mig;
			switch ($type) 
			{
				case "up":
				if($mig->up())
					shell::write("The operation for ".$subject->table." succeded! \n", "light_green");
				else
					shell::write("The operation for ".$subject->table." failed! \n", "red");
				break;

				case "down":
				if($mig->down())
					shell::write("The operation for ".$subject->table." succeded! \n", "light_green");
				else
					shell::write("The operation for ".$subject->table." failed! \n", "red");
				break;
				case "rollback":
				if($subject->version > 1)
				{
					if($name)
					{
						if(isset($v))
						{
							self::operation($subject->table, "down", $subject->version);
							self::operation($subject->table, "up", $v);
						}
						else
						{
							self::operation($subject->table, "down", $subject->version);
							self::operation($subject->table, "up", ($subject->version - 1));
						}
					}
					else
					{
						self::operation($subject->table, "down", $subject->version);
						self::operation($subject->table, "up", ($subject->version - 1));
					}
				}
				break;
			}
		}
	}
}
