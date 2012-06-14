<?php

class Client_model extends Model
{
	static $db_table = 'clients';

	function before_write()
	{
		$this->created = new Now;
	}
}