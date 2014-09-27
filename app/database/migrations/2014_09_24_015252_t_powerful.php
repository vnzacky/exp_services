<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TPowerful extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('t_powerful', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 45);
			$table->string('icon', 100)->nullable()->default('public/images/icon.png');
			$table->string('description', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('t_powerful');
	}

}
