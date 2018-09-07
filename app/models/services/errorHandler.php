<?php
/**
* This class is used to initialize the whoops pretty page handler.
* This class should be called as soon as the application loop
* begins to ensure that any exceptions thrown are cought here.
*/

namespace secc\models\services;
use secc\app;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
class errorHandler
{
	protected static $instance = null;

	public function __construct($params = null)
	{
		
	}

	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
	}
	
	public static function initialize()
	{
		$whoops 	= new Run;
		$handler 	= new PrettyPageHandler;

		$whoops->pushHandler($handler)->register();
	}
}