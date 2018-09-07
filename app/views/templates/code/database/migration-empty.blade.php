<?= '<?php' ?>

namespace secc\database;

/**
*
*/

namespace {{ $properties->namespace }};
use Illuminate\Database\Capsule\Manager as Capsule;

class {{ $properties->name."_".$properties->version }}
{
	public function up()
	{
		if(!Capsule::schema()->hasTable('{{ $properties->name }}'))
		{
			Capsule::schema()->create('{{ $properties->name }}', function($table) {
				
				$table->increments('id');
				$table->timestamps();
			});

			return true;
		}

		return false;
	}

	public function down()
	{
		if(Capsule::schema()->hasTable('{{ $properties->name }}'))
			Capsule::schema()->drop('{{ $properties->name }}'); return true;

		return false;
	}
}
