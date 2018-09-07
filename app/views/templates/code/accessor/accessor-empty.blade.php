<?= '<?php' ?>

/**
*
*/

namespace secc\models\accessors;

use secc\app;
use Illuminate\Database\Eloquent\Model as Eloquent;
class {{$name}} extends Eloquent
{

	protected static $instance 	= null;
	protected $table 			= '{{ strtolower($name) }}s';
	protected $fillable 		= [

		'created_at',
		'updated_at'
	];

	public function __construct($params = null)
	{
		
	}

	public static function instance($params = null)
	{
		return (isset($params)) ? (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance 
		: (!isset(self::$instance)) ? self::$instance = new self($params) : self::$instance;
	}
}