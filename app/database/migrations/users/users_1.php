<?php
namespace secc\database;

/**
*
*/

namespace secc\database\migrations\users;
use Illuminate\Database\Capsule\Manager as Capsule;

class users_1
{
	public function up()
	{
		if(!Capsule::schema()->hasTable('users'))
		{
			Capsule::schema()->create('users', function($table) {
				
				$table->increments('id');
				$table->timestamps();
			});

			return true;
		}

		return false;
	}

	public function down()
	{
		if(Capsule::schema()->hasTable('users'))
			Capsule::schema()->drop('users'); return true;

		return false;
	}
}
