<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

/**
 * This class does nothing. It used to create the email subscription table but would fail if the Mailing module was not
 * installed, which became a problem after open sourcing Mothership as that module was not released. It turns out that
 * the migration was pointless anyway as the columns it added are created by the mailing module anyway, but removing
 * this migration entirely would cause errors to appear when running the initial install.
 */
class _1383909255_UpdateEmailSubscription extends Migration
{
	public function up()
	{}

	public function down()
	{}
}
