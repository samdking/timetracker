<?php

class Time_model extends Model
{
	static $db_table = 'times';

	function pause()
	{
		$this->update(array('paused'=>1, 'paused_time'=>new Now));
	}

	function resume()
	{ 
		$this->secs_paused = $this->secs_paused + (time() - strtotime($this->paused_time));
		$this->paused = 0;
		$this->save();
	}

	function stop()
	{
		$this->update(array(
			'total_mins'=>floor((time() - strtotime($this->start_time) - $this->secs_paused) / 60), 
			'finished'=>1
		));
	}

	function get_current_mins()
	{
		if ($this->finished)
			return $this->total_mins;
		$paused_for = $this->paused? $this->secs_paused + (time() - strtotime($this->paused_time)) : $this->secs_paused;
		$secs = time() - strtotime($this->start_time);
		return floor(($secs - $paused_for) / 60);
	}

}