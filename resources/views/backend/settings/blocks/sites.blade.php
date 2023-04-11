<div class="box">
  <h2>Settings Edificios</h2>
  @if ($message = Session::get('success-sites'))
  <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
  </div>
  @endif
  <form method="POST" action="{{route('settings.sites.upd')}}">
    <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
    <table class="table table-hover  table-responsive">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Título</th>
          <th>URL</th>
          <th>Mail FromName </th>
          <th>Mail From</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($oSites as $v): ?>
          <tr>
            <td><input class="form-control" type="text" name="name{{$v->id}}" id="name{{$v->id}}" value="{{$v->name}}" ></td>
            <td><input class="form-control" type="text" name="title{{$v->id}}" id="title{{$v->id}}" value="{{$v->title}}" ></td>
            <td><input class="form-control" type="text" name="url{{$v->id}}" id="url{{$v->id}}" value="{{$v->url}}" ></td>
            <td><input class="form-control" type="text" name="mail_name{{$v->id}}" id="mail_name{{$v->id}}" value="{{$v->mail_name}}" ></td>
            <td><input class="form-control" type="text" name="mail_from{{$v->id}}" id="mail_from{{$v->id}}" value="{{$v->mail_from}}" ></td>
          </tr>
   
        <?php endforeach ?>
      </tbody>
    </table>
    <button class="btn btn-complete font-w400" type="submit">Guardar</button>
  </form>
</div>