<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogImages extends Model
{
    protected $table = "log_images";

	public function getAdmin()
	{
		return \App\User::find($this->admin_id);
    }
}
