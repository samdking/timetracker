<?php

class Client_model extends Model
{
	static $db_table = 'clients';

	function start_timing($user_id)
	{
		return Model::get('time')->create(array('client_id'=>$this->id, 'start_time'=>new Now, 'user_id'=>$user_id));
	}

	function create($props)
	{
		$props['created'] = new Now;
		$obj = parent::create($props);
		unset($obj->properties['created']);
		return $obj;
	}
}