<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class Listing extends Controller
{

	public function index()
	{
		return $this->render('::listing', array(
			'users'            => $users,
			'searchTerm'       => null,
			'form'             => $this->_getUploadForm(),
			'search_form'      => $this->_getSearchForm(),
		));
	}

	public function searchRedirect()
	{
		if ($search = $this->get('request')->request->get('user_search')) {
			return $this->redirect($this->generateURL('ms.cp.admin.user.search', array(
				'term' => $search['term'],
			)));
		}

		return $this->redirect($this->generateURL('ms.cp.admin.user.listing'));
	}

	public function search($term)
	{
		return $this->render('::listing', array(
			'users'            => $this->get('file_manager.file.loader')->getBySearchTerm($term),
			'searchTerm'       => $term,
			'form'             => $this->_getUploadForm(),
			'search_form'      => $this->_getSearchForm(),
		));
	}


	protected function _getSearchForm()
	{
		$form = $this->get('form')
			->setName('user_search')
			->setMethod('POST')
			->setAction($this->generateUrl('ms.cp.user.search.forward'));
		$form->add('term', 'search', 'Enter search term...');

		return $form;
	}


	public function dashboard()
	{
		return $this->render('Message:Mothership:User::User:listing:dashboard', array(
		));
	}
}
