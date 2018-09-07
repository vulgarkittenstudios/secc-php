<?php
/**
* 
*/

namespace secc\models\filters;
use secc\app;
class csrf
{
	
	private static $instance = null;

	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	public static function main()
	{
		$csrf_key = app::service('Config')->get('csrf.session');

		if(app::input()->exists())
		{
			return app::token(app::input()->post('token'));
		}
		else
		{
			return true;
		}
		
		return false;
	}
}
