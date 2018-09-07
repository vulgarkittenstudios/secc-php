<?php
/**
* This class is a wrapper around the PHPMailer
* library and is used for sending emails.
*/

namespace secc\models\services;
use secc\app;

use \PHPMailer as PHPMailer;
class mailer
{
	protected static $instance = null;
	protected static $mailer;

	public function __construct($params = null)
	{
		if(isset($params))
			self::send($params);
	}

	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
	}
	
	/**
	 * This method is used to initailize the email server
	 * 
	 * @return void Sets up PHPMailer with configurations stored in the environment.json file
	 */
	public static function initialize()
	{
		self::$mailer = new PHPMailer;

		if(Config::get('mail.smtp'))
			self::$mailer->isSMTP();

		self::$mailer->Host 		= config::get('mail.host');
		self::$mailer->SMTPAuth 	= config::get('mail.smtp');
		self::$mailer->Username 	= config::get('mail.username');
		self::$mailer->Password 	= config::get('mail.password');
		self::$mailer->SMTPSecure 	= config::get('mail.secure');
		self::$mailer->Port 		= config::get('mail.port');

		self::$mailer->isHTML(config::get('mail.html'));
	}

	/**
	 * This method is used to send email
	 * 
	 * @param  array  $data This array should contain all of the information needed to send an email
	 * @return bool       	Feedback as to whether the email sent or not
	 */
	public static function send($data = [])
	{
		self::initialize(); // Initialize all the server settings
		$data = (object)($data); // Store the data passed to this function as an object

		// Check if a from value has been supplied
		// ['from' => ['email' => 'value', 'name' => 'value']]
		if(isset($data->from))
			self::$mailer->setFrom($data->from['email'], $data->from['name']);

		// Set all the desired addresses to send the email to
		// ['addresses' => [
		// 	['email' => 'value1', 'name' => 'value1'], 
		// 	['email' => 'value2', 'name' => 'value2']]
		// ]
		if(isset($data->addresses))
		{
			for($i = 0; $i < count($data->addresses); $i++) 
			{ 
				self::$mailer->addAddress($data->addresses[$i]['email'], $data->addresses[$i]['name']);
			}
		}

		// Set all the desired replyTo addresses to give the email to
		// ['replyTo' => [
		// 	['email' => 'value1', 'name' => 'value1'], 
		// 	['email' => 'value2', 'name' => 'value2']]
		// ]
		if(isset($data->replyTo))
		{
			for($i = 0; $i < count($data->replyTo); $i++) 
			{ 
				self::$mailer->addReplyTo($data->replyTo[$i]['email'], $data->replyTo[$i]['name']);
			}
		}

		// Set all the desired cc addresses to give the email to
		// ['cc' => ['cc1@example.com', 'cc2@example.com']]
		if(isset($data->cc))
		{
			for($i = 0; $i < count($data->cc); $i++) 
			{ 
				self::$mailer->addCC($data->cc[$i]);
			}
		}

		// Set all the desired bcc addresses to give the email to
		// ['bcc' => ['bcc1@example.com', 'bcc2@example.com']]
		if(isset($data->bcc))
		{
			for($i = 0; $i < count($data->bcc); $i++) 
			{ 
				self::$mailer->addCC($data->bcc[$i]);
			}
		}

		// Set all the desired attachments addresses to give the email to
		// ['attachments' => ['/path/to/file/1', '/path/to/file/2']]
		if(isset($data->attachments))
		{
			for($i = 0; $i < count($data->attachments); $i++) 
			{ 
				self::$mailer->addCC($data->attachments[$i]);
			}
		}

		// The subject to give this email
		// ['subject' => 'The subject line']
		if(isset($data->subject))
		{
			self::$mailer->Subject = $data->subject;
		}

		// The body to give this email
		// ['body' => App::view()->make('path.to.view', ['data' => $data])]
		if(isset($data->body))
		{
			self::$mailer->Body = $data->body;
		}

		// The alt body to give this email
		// ['altBody' => 'The alt body string']
		if(isset($data->altBody))
		{
			self::$mailer->AltBody = $data->altBody;
		}

		// Send the email and return feedback as to whether it was successful or not
		if(self::$mailer->send())
			return true;
		else
			return false;
	}
}