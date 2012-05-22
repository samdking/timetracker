<?php

class Time_model extends Model
{
	static $db_table = 'times';

	function pause()
	{
		$this->pause = true;
		$this->save();
	}

	function resume($time_paused)
	{ 
		$this->mins_paused = $this->mins_paused + $time_paused;
		$this->pause = false;
		$this->save();
	}

}