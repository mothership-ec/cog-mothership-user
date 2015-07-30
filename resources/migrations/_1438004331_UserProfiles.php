<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1438004331_UserProfiles extends Migration
{
	public function up()
	{
		$this->run("
			CREATE TABLE
				user_profile
				(
					user_id INT(11) NOT NULL,
					field_name VARCHAR(255) NOT NULL,
					value_string TEXT DEFAULT NULL,
					value_int INT(11) DEFAULT NULL,
					group_name VARCHAR(255) DEFAULT NULL,
					sequence INT(11) NOT NULL DEFAULT 0,
					data_name VARCHAR(255) DEFAULT NULL,
					PRIMARY KEY (`user_id`, `field_name`, `group_name`, `sequence`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$this->run("
			CREATE TABLE
				user_type
				(
					user_id INT(11) NOT NULL,
					`type` VARCHAR(255) NOT NULL,
					PRIMARY KEY (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$this->run("
			INSERT INTO
				user_type
				(
					user_id,
					type
				)
			SELECT
				user_id,
				'none'
			FROM
				`user`
		");
	}

	public function down()
	{
		$this->run("
			DROP TABLE IF EXISTS user_profile;
		");

		$this->run("
			DROP TABLE IF EXISTS user_type;
		");
	}
}