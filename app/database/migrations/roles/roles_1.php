<?php
namespace secc\database;

/**
*
*/

namespace secc\database\migrations\roles;
use Illuminate\Database\Capsule\Manager as Capsule;

class roles_1
{
	public function up()
	{
		if(!Capsule::schema()->hasTable('roles'))
		{
			Capsule::schema()->create('roles', function($table) {
				
				$table->increments('id');
				$table->timestamps();
			});

			return true;
		}

		return false;
	}

	public function down()
	{
		if(Capsule::schema()->hasTable('roles'))
			Capsule::schema()->drop('roles'); return true;

		return false;
	}
}
