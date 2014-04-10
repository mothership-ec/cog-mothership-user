<?php

namespace Message\Mothership\User\Controller\Module\Dashboard;

use Message\Cog\Controller\Controller;
use Message\Mothership\ControlPanel\Event\Dashboard\ActivitySummaryEvent;

class UserSummary extends Controller
{
	const CACHE_KEY = 'dashboard.module.user-summary.';
	const CACHE_TTL = 60;

	/**
	 *
	 *
	 * @todo   Fire an event which the activities are loaded onto from their
	 *         respective cogules.
	 * @return
	 */
	public function index()
	{
		$user = $this->get('user.current');

		if (false === $data = $this->get('cache')->fetch(self::CACHE_KEY . $user->id)) {
			$event = new ActivitySummaryEvent;
			$event->setUser($user);

			$this->get('event.dispatcher')->dispatch(ActivitySummaryEvent::DASHBOARD_ACTIVITY_SUMMARY, $event);

			$data = [
				'activities' => $event->getActivities(),
			];

			$this->get('cache')->store(self::CACHE_KEY . $user->id, $data, self::CACHE_TTL);
		}

		return $this->render('Message:Mothership:User::module:dashboard:user-summary', [
			'user'       => $user,
			'activities' => $data['activities'],
		]);
	}
}