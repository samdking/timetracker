<?php

class Time_model extends Model
{
	static $db_table = 'times';

	function pause()
	{
		$this->update(array('paused'=>1));
	}

	function resume($time_paused)
	{ 
		$this->mins_paused = $this->mins_paused + $time_paused;
		$this->paused = 0;
		$this->save();
	}

	function stop($total_time)
	{
		$this->update(array('total_mins'=>$total_time, 'finished'=>1));
	}

}