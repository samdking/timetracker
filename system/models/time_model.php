<?php

class Time_model extends Model
{
	static $db_table = 'times';

	function pause()
	{
		$this->paused = 1;
		$this->save();
	}

	function resume($time_paused)
	{ 
		$this->mins_paused = $this->mins_paused + $time_paused;
		$this->paused = 0;
		$this->save();
	}

}