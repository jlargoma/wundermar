<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use \App\SpecialSegment;

class SpecialSegmentController extends AppController {

  public function create(Request $request) {
    $date = explode('-', str_replace('Abr', 'Apr', $request->input('fechas')));
    $start = Carbon::createFromFormat('d M, y', trim($date[0]));
    $finish = Carbon::createFromFormat('d M, y', trim($date[1]));
    $minDays = $request->all()['minDays'];

    $attributes = [
        'start' => $start,
        'finish' => $finish,
        'minDays' => intval($minDays)
    ];

    if (SpecialSegment::create($attributes))
      return redirect()->back();
  }

  public function update($id, Request $request) {
    if ($request->isMethod('get')) {
      return view('backend.segments.update', ['segment' => SpecialSegment::find($id)]);
    } elseif ($request->isMethod('post')) {
      $date = explode('-', str_replace('Abr', 'Apr', $request->input('fechas')));
      $start = Carbon::createFromFormat('d M, y', trim($date[0]));
      $finish = Carbon::createFromFormat('d M, y', trim($date[1]));
      $minDays = $request->all()['minDays'];

      $segment = SpecialSegment::find($id);
      $segment->start = $start;
      $segment->finish = $finish;
      $segment->minDays = intval($minDays);

      if ($segment->save())
        return redirect()->back();
    }
  }

  public function delete($id, Request $request) {
    $segment = SpecialSegment::find($id);
    if ($segment->delete())
      return redirect()->back();
  }

  public static function checkDates($start, $finish) {
    $dateStart = $start->copy();

    for ($i = 1; $i < $start->diffInDays($finish); $i++) {
      $segment = SpecialSegment::where('start', '<=', $dateStart->copy()->format('Y-m-d'))
              ->where('finish', '>=', $dateStart->copy()->format('Y-m-d'))
              ->first();

      if ($segment)
        return $segment;
    }

    return false;
  }

}
