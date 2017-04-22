<?php

namespace Scrumplex\PASSY;


class Response
{

	private $success;
	private $data;

	function __construct($success, $data)
	{
		$this->success = $success;
		$this->data = $data;
	}

	function getJSONResponse()
	{
		return json_encode(array(
			"success" => $this->success,
			"timestamp" => time(),
			"msg" => $this->success ? "success" : $this->data,
			"data" => !$this->success ? null : $this->data,
		));
	}

	/**
	 * @return boolean
	 */
	public function wasSuccess()
	{
		return $this->success;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}


}