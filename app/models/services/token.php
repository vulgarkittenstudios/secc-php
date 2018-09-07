<?php
/**
* This class is used to create
* and check tokens for use
* against CSRF attacks.
*/

namespace secc\models\services;
use secc\app;
class token
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
	
	/**
	 * This method generates a hidden input with a random token value in the $_SESSION superglobal
	 * 
	 * @return String the html input with the randomly generated token string
	 */
	public static function generate()
	{
		$token = session::put(config::get('csrf.session'), hash::make(hash::random(128)));
		return '<input type="hidden" id="token" name="token" value="'.$token.'">';
	}

	/**
	 * This method is used to check a token against it's session counter part
	 * and delete it if it exists so that it can't be used again.
	 * 
	 * @param  String $token The token to check
	 * @return Bool          Feedback as to whether the check passed
	 */
	public static function check($token)
	{
		$tokenName = config::get('csrf.session');

		if(session::exists($tokenName) && $token === session::get($tokenName))
		{
			session::delete($tokenName);
			return true;
		}

		return false;
	}
}