<?php

class DB
{
	private static $instance;
	private $handle;

	private function __construct()
	{
		global $db;
		$this->handle = new mysqli($db['server'], $db['user'], $db['pass'], $db['name']);
		$this->handle->set_charset("utf8");
	}

	static function init()
	{
		self::$instance = self::$instance? self::$instance : new self;
		return self::$instance; 
	}

	function query($query)
	{
		$mysql_query = $this->handle->query($query);
		if ($mysql_query) return $mysql_query;
	}

	function last_id()
	{
		return $this->handle->insert_id;
	}

	function error()
	{
		return $this->handle->error;
	}
	
	function result($result) 
	{
		while($row = $result->fetch_assoc())
			$data[] = $row;
		return isset($data)? $data : array();
	}

}

class SQLCommand
{
	protected $command;

	function __toString()
	{
		return $this->command . '()';
	}
}

class Now extends SQLCommand
{
	protected $command = 'NOW';
}