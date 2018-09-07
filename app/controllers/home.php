<?php
/*
*
*/

namespace secc\controllers;
use secc\app;
class home
{
	public static $instance = null;

	public static function instance()
	{
		return (!isset(self::$instance)) ? self::$instance = new self : self::$instance;
	}

	public static function index()
	{
		app::view()->render('templates.frontend.pages.home');
	}
}
