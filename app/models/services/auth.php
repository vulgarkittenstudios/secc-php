<?php
/**
* This class is used to access log users in/out of the application,
* track and maintain their data, and manage their accounts.
*/

namespace secc\models\services;
use secc\app;
class auth
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
	
	public static function attempt($identifier, $password)
	{
		$user = app::accessor('user');
		$user = $user->where('email', $identifier)->orWhere('username', $identifier)->first();

		if(!$user)
			return false;

		if(hash::password_verify($password, $user->password))
		{
			if($user->active == true)
			{
				session::put(config::get('auth.session'), $user->id);
				return true;
			}
			else
			{
				session::flash('error-message', 'You need to activate your account before you can log in.');
				redirect::to(route::url('system-message'));
			}
		}

		return false;
	}

	public static function user()
	{
		if(self::check())
			return app::accessor('user')->find(session::get(config::get('auth.session')));
	}

	public static function check()
	{
		return (session::exists(config::get('auth.session'))) ? true : false;
	}

	public static function activate($username, $hash)
	{
		$user = app::accessor('user')->where('username', $username)->where('hash', $hash)->first();
		if($user) 
		{
			$user->hash = '';
			$user->active = true;
			$user->save();
			session::flash('success-message', 'Your account is now active. You may now <a href="'.route::url('login').'">log in</a>.');
			redirect::to(route::url('system-message'));
		}
		else redirect::to(401);
	}

	public static function permission($string = '')
	{
		$user = self::user();
		$role = app::accessor('role')->where('id', $user->role)->first();

		if($role)
		{
			$role = (object)json_decode($role);
			$permissions = (json_decode($role->permissions));
			
			if($permissions->{$string})
				return true;

			return false;
		}
		else return false;
	}

	public static function logout()
	{
		session::delete(config::get('auth.session'));
	}
}