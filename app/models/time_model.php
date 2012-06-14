<?php

class Time_model extends Model
{
	static $db_table = 'times';

	function client()
	{
		return Model::get('client')->find($this->client_id);
	}

}