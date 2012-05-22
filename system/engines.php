<?php

class Engine
{
	function __construct()
	{
		if (method_exists($this, 'init'))
			$this->init();
	}

	static function get($type)
	{
		$class_name = $type . 'engine';
		return new $class_name;
	}
}

class MySQLEngine extends Engine
{

	private $sql = array();
	private $query_type;
	private $handle;
	private $result;

	function init()
	{
		global $db;
		$this->handle = new mysqli($db['server'], $db['user'], $db['pass'], $db['name']);
		$this->handle->set_charset("utf8");
	}

	private function construct_sql()
	{
		switch ($this->query_type) {
			case 'insert':
				$sql = 'INSERT INTO ' . $this->sql['table'] . ' SET ' . $this->sql['set'];
			break;
			case 'update':
				$sql = 'UPDATE ' . $this->sql['table'] . ' SET ' . $this->sql['set'];
			break;
			case 'select':
			default:
				$sql = 'SELECT * FROM ' . $this->sql['table'];
			break;
		}
		
		switch ($this->query_type) {
			case 'update':
			case 'select':
			default:
				if (isset($this->sql['where']))
					$sql .= ' WHERE ' . $this->sql['where'];
			break;
		}
		
		switch ($this->query_type) {
			case 'select':
			default:
				if (isset($this->sql['order']))
					$sql .= ' ORDER BY ' . $this->sql['order'];
				if (isset($this->sql['limit']))
					$sql .= ' LIMIT ' . $this->sql['limit'];
			break;
		}

		return $sql;
	}

	function result()
	{
		$this->execute();
		if (is_object($this->result))
			while($row = $this->result->fetch_assoc())
				$data[] = $row;
		return isset($data)? $data : array();		
	}

	function execute()
	{
		$sql = $this->construct_sql();
		echo '<p>' . $sql . '</p>';
		$this->result = $this->handle->query($sql);
		if ($error = $this->handle->error)
			throw new Exception($error . ' in ' . $sql);
		return $this;
	}

	function last_id()
	{
		$this->handle->insert_id;
	}

	function from($value)
	{
		$this->sql['table'] = $value;
	}

	function limit($val)
	{
		$this->sql['limit'] = isset($this->sql['limit'])? $this->sql['limit'] . ', ' . $val : $val;
	}

	function order($val)
	{
		$this->sql['order'] = $val;
	}

	function offset($val)
	{
		$this->sql['limit'] = isset($this->sql['limit'])? $val . ', ' . $this->sql['limit'] : $val;
	}

	function insert($vals)
	{
		$this->sql['set'] = $this->params($vals);
		$this->query_type = 'insert';
		return $this;
	}

	function update($vals, $conditions = false)
	{
		$this->sql['set'] = $this->params($vals);
		if ($conditions)
			$this->sql['where'] = $this->params($conditions);
		$this->query_type = 'update';
		return $this;
	}

	function delete($conditions)
	{
		$this->sql['where'] = $this->params($conditions);
		$this->query_type = 'delete';
	}

	function select($values)
	{
		$this->sql['select'] = $values;
	}

	function where($conditions)
	{
		$this->sql['where'] = $this->params($conditions);
	}

	private function params($params)
	{
		foreach($params as $key=>$val) {
			$val = is_string($val) || is_null($val)? "'$val'" : $val;
			$conditions[] = "`$key` = " . $val;
		}
		return implode(', ', $conditions);
	}
}