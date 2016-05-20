<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1454076190639_UpdateUserTitle extends Migration
{
  public function up()
  {
	// convert 'Doctor' to 'Dr' in database
	$this->run("
		UPDATE user SET title = REPLACE (
			title,
			'Doctor',
			'Dr'
		);
	");

	// convert all titles to Title Case
	$this->run("
		UPDATE user SET title = CONCAT(
			UCASE(LEFT(title, 1)),
			SUBSTRING(title, 2)
		);
	");
  }

  public function down()
  {
	// convert 'Dr' to 'Doctor' in database (inverse of above)
	$this->run("
		UPDATE user SET title = REPLACE (
			title,
			'Dr',
			'Doctor'
		);
	");
  }
}
