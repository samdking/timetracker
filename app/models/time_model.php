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

	function paused_time()
	{		
		$paused_duration = $this->paused? time() - strtotime($this->paused_time) : 0;
		return 1000 * ($this->secs_paused + $paused_duration);
	}

}