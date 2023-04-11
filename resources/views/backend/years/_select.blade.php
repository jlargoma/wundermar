<?php 
$years = \App\Years::all();
$activeY = getYearActive();
?>
<select name="years" class="form-control minimal s_years">
    @foreach($years as $key => $year)
        <option value="{{ $year->id }}" @if ($year->id == $activeY) selected @endif >
            {{ $year->year }} - {{ $year->year + 1 }}
        </option>
    @endforeach
</select>