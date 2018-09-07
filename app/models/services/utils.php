<?php
/**
*
*/

namespace secc\models\services;
use secc\app;
class utils
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

	public static function arrayDuplicates($array)
	{
		$counts = array_count_values($array);
		return $filtered = array_filter($array, function ($value) use ($counts) {
		    return $counts[$value] > 1;
		});
	}

	public static function fileAge($file = '')
	{
		return date('F d Y H:i:s', filemtime($file));
	}

	/** 
	 * recursively create a long directory path
	 */
	public static function mkdir($path) 
	{
	    if (is_dir($path)) return true;
	    $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
	    $return = self::mkdir($prev_path);
	    return ($return && is_writable($prev_path)) ? mkdir($path) : false;
	}

	public static function scandirRecursive($dir)
	{
		$files = [];
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $filename)
		{
		    if ($filename->isDir()) continue;
		    $files[] = $filename;
		}

		return $files;
	}
}