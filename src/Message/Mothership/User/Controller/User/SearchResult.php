<?php

namespace Message\Mothership\User\Controller\User;

use Message\Cog\Controller\Controller;

class SearchResult extends Controller
{
	public function index()
	{
		return $this->render('Message:Mothership:User::User:listing:search-result');
	}

}