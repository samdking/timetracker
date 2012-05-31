<?php

class Time_model extends Model
{
	static $db_table = 'times';

	function pause()
	{
		$this->update(array('paused'=>1, 'paused_time'=>new Now));
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

	function get_current_mins()
	{
		if ($this->finished)
			return $this->total_mins - $this->mins_paused;
		$paused_mins = $this->paused? $this->mins_paused + floor((time() - strtotime($this->paused_time)) / 60) : (int)$this->mins_paused;
		$mins = floor((time() - strtotime($this->start_time)) / 60);
		return $mins - $paused_mins;
	}

}