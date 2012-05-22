<?php

class Query_set
{
	private $model;
	private $engine;
	private $single_result;

	function __construct($model)
	{
		$this->model = $model;
		$this->engine = $model::engine();
	}

	function find($value)
	{
		$this->engine->select(array('id'=>$value));
	}

	function first()
	{
		$this->limit(1);
		$objects = $this->make_objects($this->engine->result());
		return $objects? reset($objects) : NULL;
	}

	function last()
	{
		$this->limit(1);
		$this->order('id desc');
		$objects = $this->make_objects($this->engine->result());
		return $objects? reset($objects) : NULL;
	}

	function make_objects($arr)
	{
		if (empty($arr))
			return false;
		foreach($arr as $data) {
			$obj = new $this->model;
			$obj->populate($data);
			$objects[] = $obj;
		}
		return $objects;
	}

	function order($value)
	{
		$this->engine->order($value);
		return $this;
	}

	function first_or_create($params)
	{
		$obj = $this->filter($params)->first();
		if (!$obj) {
			$obj = new $this->model;
			$obj->create($params);
		}
		return $obj;
	}

	function limit($params)
	{
		$this->engine->limit($params);
		return $this;
	}

	function filter($where)
	{
		$this->engine->where($where);
		return $this;
	}
}