<?php

namespace Message\Mothership\User\Controller\Module\Dashboard;

use Message\Cog\Controller\Controller;
use Message\Mothership\ControlPanel\Event\Dashboard\ActivitySummaryEvent;

class UserSummary extends Controller
{
	/**
	 * Build the user activity dashboard with an ActivitySummaryEvent.
	 *
	 * @return \Message\Cog\HTTP\Response
	 */
	public function index()
	{
		$user = $this->get('user.current');

		$event = new ActivitySummaryEvent;
		$event->setUser($user);

		$this->get('event.dispatcher')->dispatch(ActivitySummaryEvent::DASHBOARD_ACTIVITY_SUMMARY, $event);

		$data = [
			'activities' => $event->getActivities(),
		];

		return $this->render('Message:Mothership:User::module:dashboard:user-summary', [
			'user'       => $user,
			'activities' => $data['activities'],
		]);
	}
}