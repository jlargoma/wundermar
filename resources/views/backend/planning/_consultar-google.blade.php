@extends('layouts.popup')

@section('title') Administrador de reservas @endsection

@section('externalScripts')
@endsection

@section('content')
<div class="row">
  <?php foreach ($sites as $id=>$name): ?>
    <?php if(isset($urls[$id])): ?>
  <a class="btn btn-success btn-block" href="{{$urls[$id]}}">{{$name}}</a>
    <?php else: ?>
      <button disabled="" class="btn btn-success btn-block">{{$name}}</button>
    <?php endif ?>
  <?php endforeach; ?>
  
</div>
@endsection
