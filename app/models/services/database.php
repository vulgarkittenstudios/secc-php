<?php
/**
*
*/

namespace secc\models\services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class database extends Capsule
{
	protected static $capsule;
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
	
	public static function initialize()
	{
		self::$capsule = new Capsule;
		
		self::$capsule->addConnection([

			'driver'	=> config::get('db.driver'),
			'host'		=> config::get('db.host'),
			'database'	=> config::get('db.name'),
			'username'	=> config::get('db.username'),
			'password'	=> config::get('db.password'),
			'charset'	=> config::get('db.charset'),
			'collation'	=> config::get('db.collation'),
			'prefix'	=> config::get('db.prefix')
		]);

		self::$capsule->setEventDispatcher(new Dispatcher(new Container));
		self::$capsule->setAsGlobal();
		self::$capsule->bootEloquent();
	}
}
