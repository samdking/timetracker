<?php

class Model
{
	private $properties;

	static function get($model)
	{
		$class_name = $model . '_model';
		include 'models/' . $class_name . '.php';
		return new $class_name;
	}

	function __get($prop)
	{
		if (isset($properties[$prop]))
			return $this->properties[$prop];
	}

	function __set($prop, $value)
	{
		$this->properties[$prop] = $value;
	}

	function populate($props)
	{
		$this->properties = $props;
	}

	function find($params = array())
	{
		foreach($params as $key=>$val) {
			$val = is_string($val)? "'$val'" : $val;
			$where[] = "`$key` = " . $val;
		}
		$rows = DB::init()->result(DB::init()->query('SELECT * FROM ' . static::$db_table . (isset($where)? ' WHERE ' . implode(', ', $where) : '')));
		foreach($rows as $row) {
			$obj = new $this;
			$obj->populate($row);
			$list[] = $obj;
		}
		return isset($list)? $list : array();
	}

	function save()
	{
		if ($this->id)
			DB::init()->update($this->properties, array('id'=>$this->id));
		else
			DB::init()->insert($this->properties);
	}

	function last()
	{
		return $this->one()->order('id DESC');
	}

	function first_or_create($params)
	{
		$obj = $this->find($params)->limit(1);
		if (!$obj)
			$obj = $this->create($params);
		return $obj;
	}

	function create($params)
	{
		$params['created'] = new Now;
		foreach($params as $key=>$val) {
			$val = is_string($val)? "'$val'" : $val;
			$where[] = "`$key` = " . $val;
		}
		DB::init()->query('INSERT INTO ' . static::$db_table . ' SET ' . implode(', ', $where));
		$params['id'] = DB::init()->last_id();
		$obj = new $this;
		$obj->populate($params);
		return $obj;
	}
}