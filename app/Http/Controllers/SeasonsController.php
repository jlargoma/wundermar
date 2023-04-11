<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Carbon\Carbon;

class SeasonsController extends AppController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return redirect()->action('PricesController@index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		$date   = explode('-', str_replace('Abr', 'Apr', $request->input('fechas')));
		$start  = Carbon::createFromFormat('d M, y', trim($date[0]));
		$finish = Carbon::createFromFormat('d M, y', trim($date[1]));

		$exist = \App\Seasons::existDate($start->copy()->format('d/m/Y'), $finish->copy()->format('d/m/Y'));
		if ($exist == false)
		{
			$seasons              = new \App\Seasons();
			$seasons->start_date  = $start;
			$seasons->finish_date = $finish;
			$seasons->type        = $request->input('type');
			if ($seasons->save())
			{
				return redirect()->action('SeasonsController@index');
			}
		} else
		{
			echo "La fecha ya esta ocupada";
		}
	}

	public function createType(Request $request)
	{
		$existTypeSeason = \App\TypeSeasons::where('name', $request->input('name'))->count();
		if ($existTypeSeason == 0)
		{
			$typeSeasons = new \App\TypeSeasons();

			$typeSeasons->name = $request->input('name');

			if ($typeSeasons->save())
			{
				for ($i = 4; $i <= 8; $i++)
				{
					$price             = new \App\Prices();
					$price->season     = $typeSeasons->id;
					$price->occupation = $i;
					$price->save();
				}


				return redirect()->action('SeasonsController@index');
			}
		} else
		{
			echo "Ya existe este tipo";
		}

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int                      $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if ($request->isMethod('get'))
		{
			return view('backend.seasons.update', ['season' => \App\Seasons::find($id)]);

		} elseif ($request->isMethod('post'))
		{
			$date    = explode('-', str_replace('Abr', 'Apr', $request->input('fechas')));
			$start   = Carbon::createFromFormat('d M, y', trim($date[0]));
			$finish  = Carbon::createFromFormat('d M, y', trim($date[1]));
			$type = $request->all()['type'];

			$segment              = \App\Seasons::find($id);
			$segment->start_date  = $start;
			$segment->finish_date = $finish;
			$segment->type        = intval($type);

			if ($segment->save())
				return redirect()->back();
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function delete($id)
	{
		$season = \App\Seasons::find($id);

		if ($season->delete())
		{
			return redirect()->action('SeasonsController@index');
		}

	}
}
