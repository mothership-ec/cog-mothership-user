<?php

namespace Message\Mothership\User;

use Message\User\AnonymousUser;

use Message\Cog\Event\EventListener as BaseListener;
use Message\Cog\Event\SubscriberInterface;
use Message\Cog\HTTP\RedirectResponse;

use Message\Mothership\ControlPanel\Event\Dashboard\DashboardEvent;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\KernelEvent;

use Message\Mothership\Report\Event as ReportEvents;

/**
 *
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class EventListener extends BaseListener implements SubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			KernelEvents::REQUEST => array(
				array('checkLoggedIn')
			),
			DashboardEvent::DASHBOARD_INDEX => array(
				'buildDashboardIndex'
			),
			ReportEvents\Events::REGISTER_REPORTS => [
				'registerReports'
			]
		);
	}

	/**
	 * Check if the user is logged in when requesting an account route, if not
	 * redirect to the homepage.
	 *
	 * @param  Event  $event
	 */
	public function checkLoggedIn(KernelEvent $event)
	{
		$user = $this->get('user.current');

		if ($user instanceof AnonymousUser and
			is_array($event->getRequest()->get('_route_collections')) and
			in_array('ms.user.account', $event->getRequest()->get('_route_collections'))
		) {
			$event->setResponse(new RedirectResponse('/'));
		}
	}

	/**
	 * Add controller references to the dashboard index.
	 *
	 * @param  DashboardEvent $event
	 */
	public function buildDashboardIndex(DashboardEvent $event)
	{
		$event->addReference('Message:Mothership:User::Controller:Module:Dashboard:UserSummary#index');
	}

	public function registerReports(ReportEvents\BuildReportCollectionEvent $event)
	{
		foreach ($this->get('user.reports') as $report) {
			$event->registerReport($report);
		}
	}
}