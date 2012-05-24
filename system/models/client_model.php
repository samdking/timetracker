<?php

class Client_model extends Model
{
	static $db_table = 'clients';

	function start_timing()
	{
		Model::get('time')->create(array('client_id'=>$this->id, 'start_time'=>new Now));
	}

	function create($props)
	{
		$props['created'] = new Now;
		parent::create($props);
		unset($this->properties['created']);
	}
}