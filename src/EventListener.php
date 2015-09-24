<?php

namespace Message\Mothership\User;

use Message\User\Event\Event as UserEvent;
use Message\User\AnonymousUser;

use Message\Cog\Event\EventListener as BaseListener;
use Message\Cog\Event\SubscriberInterface;
use Message\Cog\HTTP\RedirectResponse;

use Message\Mothership\ControlPanel\Event\Dashboard\DashboardEvent;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\KernelEvent;

use Message\Mothership\Report\Event as ReportEvents;
use Message\Mothership\Report\Filter\ModifyQueryInterface;

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
		return [
			KernelEvents::REQUEST => array(
				array('checkLoggedIn')
			),
			DashboardEvent::DASHBOARD_INDEX => array(
				'buildDashboardIndex'
			),
			ReportEvents\Events::REGISTER_REPORTS => [
				'registerReports'
			],
			Events::USER_SUMMARY_REPORT => [
				'applyReportFilters'
			],
			UserEvent::CREATE => [
				'setProfileType'
			],
		];
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

	public function applyReportFilters(ReportEvents\ReportEvent $event)
	{
		foreach ($event->getQueryBuilders() as $queryBuilder) {
			foreach ($event->getFilters() as $filter) {
				if ($filter instanceof ModifyQueryInterface) {
					$filter->apply($queryBuilder);
				}
			}
		}
	}

	public function setProfileType(UserEvent $event)
	{
		$profile = $this->get('user.profile.factory')->getProfile('none');
		$this->get('user.profile.type.edit')->save($event->getUser(), $profile->getType());
	}
}