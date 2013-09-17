<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Listing extends Controller
{

	public function dashboard()
	{
		return $this->render('Message:Mothership:User::User:listing:dashboard');
	}

	public function searchForm()
	{
		$form = $this->_getSearchForm();

		return $this->render('Message:Mothership:User::User:listing:search-form', array(
			'search_form' => $form
		));
	}

	public function search()
	{
		$form = $this->_getSearchForm();
		$data = $form->getFilteredData();
		$users = $this->get('user.loader')->getBySearchTerm($data['term']);

		return $this->render('Message:Mothership:User::User:listing:search-result', array(
			'term' => $data['term'],
			'users' => $users,
		));
	}

	protected function _getSearchForm()
	{
		$form = $this->get('form')
			->setMethod('GET')
			->setAction($this->generateUrl('ms.cp.user.search'));

		$form->add('term', 'search', 'Enter search terms...');

		return $form;
	}

}
