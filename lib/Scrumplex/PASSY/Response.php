<?php

namespace Scrumplex\PASSY;


class Response
{

	/**
	 * @var bool
	 */
	private $success;
	/**
	 * @var mixed
	 */
	private $data;

	/**
	 * Response constructor.
	 * @param boolean $success Defines if request was successful or not
	 * @param mixed $data The data to be returned. It should be array of string. If $success is false the data will be returned under "msg" field of the JSON.
	 */
	function __construct($success, $data)
	{
		$this->success = $success;
		$this->data = $data;
	}

	/**
	 * @return string json encoded string of response
	 */
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