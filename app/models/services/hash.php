<?php
/**
* This class is used to generate and manage
* hashes. It can be used to generate
* random strings or encrypt strings.
*/

namespace secc\models\services;

use secc\app;
use RandomLib\Factory as RandomLib;
class hash
{
	public static $factory;
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
	 * This method is used to hash a password
	 * with the blowfish encryption algorythm
	 * 
	 * @param  String $password The password to encryp
	 * @return String           The encrypted password
	 */
	public static function password($password)
	{
		// Use the PHP password hashing API to encrypt a password
		return password_hash($password, (int)(Config::get('hash.algo')), [

			'cost'	=> (int)(config::get('hash.cost'))
		]);
	}

	/**
	 * This mehtod is used to verify a string
	 * against a blowfish encrypted version
	 * of a string, usually a password.
	 * @param  String $password The string to verify
	 * @param  String $hash     The hashed version of the string
	 * @return Bool             Feedback as to whether the string matches 
	 */
	public static function password_verify($password, $hash)
	{
		// Use the PHP password hashing API to verify a password string
		return password_verify($password, $hash);
	}

	/**
	 * This method is used to turn a string into
	 * an encrypted string with an SHA256 salted 
	 * version.
	 * 
	 * @param  String $input The string to encrypt
	 * @return String        The encrypted version
	 */
	public static function make($input)
	{
		return hash('sha256', $input);
	}

	/**
	 * This method is used to decrypt an SHA256
	 * encrypted string.
	 * 
	 * @param  String $stored The encrypted version of the string
	 * @param  String $user   The string to check it agains
	 * @return Bool           Feedback as to whether we dycrypted the string
	 */
	public static function verify($stored, $user)
	{
		return hash_equals($stored, $user);
	}

	/**
	 * This method is used to create a secure 
	 * random string of a specified length
	 * @param  Int $length The length of the string
	 * @return String      The random string
	 */
	public static function random($length)
	{
		self::$factory = new RandomLib;
		$generator = self::$factory->getMediumStrengthGenerator();

		return $generator->generateString($length, "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
	}
}