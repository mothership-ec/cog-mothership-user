<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Listing extends Controller
{

	public function searchAction()
	{

	}

	public function tabs($userID)
	{
		$tabs = array(
			'Details' 		=> $this->generateUrl('ms.user.admin.detail.edit', 	  array('userID' => $user->id)),
			'Addresses'	 	=> $this->generateUrl('ms.user.admin.address.edit',   array('userID' => $user->id)),
			'Order History' => $this->generateUrl('ms.user.admin.order-history',  array('userID' => $user->id)),
		);
		
		$current = ucfirst(trim(strrchr($this->get('http.request.master')->get('_controller'), '::'), ':'));
		
		return $this->render('Message:Mothership:User::listing:tabs', array(
			'tabs'    => $tabs,
			'userID'  => $userID,
		));
	}

	public function sidebar()
	{
		$search_form = $this->form();

		return $this->render('Message:Mothership:User::User:listing:sidebar', array(
 			'search_form'    => $search_form,
		));
	}

	public function dashboard()
	{
		return $this->render('Message:Mothership:User::User:listing:dashboard', array(
		));
	}

	protected function _getSearchForm()
	{
		$form = $this->get('form')
			->setName('order_search')
			->setMethod('POST')
			->setAction($this->generateUrl('ms.cp.user.search.action'));
		$form->add('term', 'search', 'Search');

		return $form;
	}


}
