<?php

class Model
{
	protected $properties;
	protected static $engine;

	static function get($model)
	{
		$class_name = $model . '_model';
		include 'models/' . $class_name . '.php';
		return new $class_name;
	}

	function __call($method, $args)
	{
		$queryset = $this->query_set();
		if (method_exists($queryset, $method))
			return call_user_func_array(array($queryset, $method), $args);
		return $this;
	}

	function __get($prop)
	{
		if (isset($this->properties[$prop]))
			return $this->properties[$prop];
	}

	function __set($prop, $value)
	{
		$this->properties[$prop] = $value;
	}

	function query_set()
	{
		return new Query_set(get_class($this));
	}

	static function engine()
	{
		$engine = Engine::get('mysql');
		$engine->from(static::$db_table);
		return $engine;
	}

	function populate($props)
	{
		$this->properties = $props;
	}

	function save()
	{
		if ($this->id)
			$this->update();
		else
			$this->create($this->properties);
	}

	function update()
	{
		static::engine()->update($this->properties, array('id' => $this->id))->execute();
	}

	function create($props)
	{
		//$params['created'] = new Now;
		$params['id'] = static::engine()->insert($props)->execute()->last_id();
		$obj = new $this;
		$obj->populate($props);
		return $obj;
	}

}