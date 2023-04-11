<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentsRooms extends Model
{
	protected $fillable = [
		"room_id",
		"user_id",
		"agency_id"
	];
	public function room()
	{
		return $this->hasOne('\App\Rooms', 'id', 'room_id');
	}

	public function user()
	{
		return $this->hasOne('\App\User', 'id', 'user_id');
	}

}
