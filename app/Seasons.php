<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class Seasons extends Model
{
	static function existDate($start, $finish)
	{
		$existStart  = false;
		$existFinish = false;

		$date   = new Carbon('first day of September 2018');
		$fechas = \App\Seasons::where('start_date', '>=', $date->copy())
		                      ->where('finish_date', '<=', $date->copy()->addYear())
		                      ->get();

		$requestStart  = Carbon::createFromFormat('d/m/Y', $start);
		$requestFinish = Carbon::createFromFormat('d/m/Y', $finish);

		foreach ($fechas as $fecha)
		{
			if ($existStart == false && $existFinish == false)
			{
				$start  = Carbon::createFromFormat('Y-m-d', $fecha->start_date);
				$finish = Carbon::createFromFormat('Y-m-d', $fecha->finish_date);

				$existStart  = Carbon::create($requestStart->year, $requestStart->month, $requestStart->day)
				                     ->between($start, $finish);
				$existFinish = Carbon::create($requestFinish->year, $requestFinish->month, $requestFinish->day)
				                     ->between($start, $finish);
			}
		}
		if ($existStart == false && $existFinish == false)
		{
			return false;
		} else
		{
			return True;
		}
	}


	public function typeSeasons()
	{
		return $this->hasOne('\App\TypeSeasons', 'id', 'type');
	}


	public static function getSeasonType($start)
	{
		return \App\Seasons::select('type')
			->where('start_date', '<=', $start)
			->where('finish_date', '>=', $start)
			->first()
			->type ?? 0;
	}
}
