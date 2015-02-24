<?php

namespace Message\Mothership\User\Bootstrap;

use Message\Cog\Bootstrap\TasksInterface;
use Message\Mothership\User\Task;

class Tasks implements TasksInterface
{
    public function registerTasks($tasks)
    {
		// User tasks
		$tasks->add(new Task\CreateAdminUser('user:create_admin'), 'Create an admin user via the command line');
	}
}