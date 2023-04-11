<div class="box">
  <h2>General Settings</h2>
  @if ($message = Session::get('success-gral'))
  <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
  </div>
  @endif
  <form method="POST" action="{{route('settings.gral.upd')}}">
    <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
    <table class="table table-hover  table-responsive">
      <tbody>
        <?php foreach ($general as $k => $v): ?>
          <tr>
            <td >{{$v['label']}}</td>
            <td class="text-center" >
              <input class="form-control" type="text" name="{{$k}}" id="{{$k}}" value="{{$v['val']}}" >
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <button class="btn btn-complete font-w400" type="submit">Guardar</button>
  </form>
</div>