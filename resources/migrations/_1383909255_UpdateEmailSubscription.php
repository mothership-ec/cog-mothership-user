<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1383909255_UpdateEmailSubscription extends Migration
{
	public function up()
	{
		$this->run("
			ALTER `email_subscription`
			ADD `subscribed` tinyint(1) DEFAULT 0,
			ADD `updated_at` int(11) unsigned DEFAULT NULL,
			ADD `updated_by` int(11) unsigned DEFAULT NULL;
		");
	}

	public function down()
	{
		$this->run('
			ALTER `email_subscription`
			DROP `subscribed`,
			DROP `updated_at`,
			DROP `updated_by`;
		');
	}
}
