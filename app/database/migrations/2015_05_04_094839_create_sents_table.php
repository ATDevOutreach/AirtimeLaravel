<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('status');
	   		$table->string('amount');
            $table->string('phoneNumber');
    	    $table->string('requestId');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sents');
	}

}
