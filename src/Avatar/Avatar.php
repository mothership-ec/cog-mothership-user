<?php

namespace Message\Mothership\User\Avatar;

class Avatar
{
	private $_url;
	private $_size;
	private $_default;

	/**
	 * @param mixed $url
	 *
	 * @return Avatar         return $this for chainability
	 */
	public function setUrl($url)
	{
		$this->_url = $url;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @param mixed $size
	 *
	 * @return Avatar         return $this for chainability
	 */
	public function setSize($size)
	{
		$this->_size = $size;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->_size;
	}


	/**
	 * @param mixed $default
	 *
	 * @return Avatar         return $this for chainability
	 */
	public function setDefault($default)
	{
		$this->_default = $default;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDefault()
	{
		return $this->_default;
	}
}