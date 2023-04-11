<ul class="nav navbar-nav navbar-left">
<?php $uRole = getUsrRole(); ?>
<?php $pathRequest = Request::path(); ?>

  <?php if ($uRole == "propietario"): ?>
    @yield('nav_link')
  <?php endif ?>  
  <?php if ($uRole == "limpieza"): ?>
    <li class="{{ $pathRequest == 'admin/limpieza' ? 'active' : '' }}">
        <a href="{{ url('admin/limpieza') }}" class="detailed">Planning</a>
    </li>
  <?php endif ?>
  <?php if ($uRole == "admin" || $uRole == "subadmin" || $uRole == "recepcionista"): ?>
    <li class="{{ $pathRequest == 'admin/reservas' ? 'active' : '' }}">
      <a href="{{ url('admin/reservas') }}" class="detailed">Reservas</a>
    </li>
    <li class="{{ $pathRequest == 'admin/liquidacion'  ? 'active' : '' }}">
      <a href="{{ url('admin/liquidacion') }}" class="detailed">Liq. por reservas</a>
    </li>
    <?php if ($uRole == "admin"): ?>
    <li class="{{ $pathRequest == 'admin/usuarios' ? 'active' : '' }}">
      <a href="{{ url('admin/usuarios') }}"  class="detailed">Usuarios</a>
    </li>

    <li class="{{ $pathRequest == 'admin/clientes' ? 'active' : '' }}">
      <a href="{{ url('admin/clientes') }}" class="detailed">Clientes</a>
    </li>
    <?php endif ?>
    
    <li class="{{ $pathRequest == 'admin/apartamentos' ? 'active' : '' }}">
      <a href="{{ url('admin/apartamentos') }}" class="detailed">Aptos</a>
    </li>
     <li class="{{ $pathRequest == 'admin/facturas' ? 'active' : '' }}">
      <a href="{{ url('admin/facturas') }}"  class="detailed">Facturas</a>
    </li>
    <li class="{{ $pathRequest == 'admin/settings' ? 'active' : '' }}">
      <a href="{{ url('admin/settings') }}" class="detailed">Settings</a>
    </li>
    <li class="{{ Request::path() == 'admin/settings_msgs' ? 'active' : '' }}">
        <a href="{{ url('admin/settings_msgs') }}" class="detailed">Txt Email</a>
    </li>
     <li class="{{ $pathRequest == 'admin/orders-payland' ? 'active' : '' }}">
          <a href="{{ url('admin/orders-payland') }}" class="detailed">PAYLAND</a>
    </li>
    <li class="{{ $pathRequest == 'admin/limpiezas' ? 'active' : '' }}">
        <a href="{{ url('admin/limpiezas/') }}" class="detailed">Limpiezas</a>
    </li>
    <li class="{{ $pathRequest == 'admin/caja' ? 'active' : '' }}">
        <a href="{{ url('admin/caja/') }}" class="detailed">Caja</a>
    </li>
    <li class="{{ $pathRequest == 'admin/excursiones' ? 'active' : '' }}">
        <a href="{{ url('admin/excursiones') }}" class="detailed">Excursiones</a>
    </li>
    <li class="{{  (preg_match('/\/show-INE/i',$pathRequest))  ? 'active' : '' }}">
        <a href="{{ url('/admin/show-INE') }}" class="detailed">Estad. INE</a>
    </li>
    <li class="{{  (preg_match('/\/revenue/i',$pathRequest))  ? 'active' : '' }}">
        <a href="{{ url('/admin/revenue/DASHBOARD') }}" class="detailed">REVENUE</a>
    </li>
    <li class="{{  (preg_match('/\/channel-manager/i',$pathRequest) || $pathRequest == 'admin/precios')  ? 'active' : '' }}">
        <a href="{{ url('/admin/precios') }}" class="detailed">CHANNEL</a>
    </li>
    <li class="{{ $pathRequest == 'admin/contabilidad'  ? 'active' : '' }}">
      <a href="{{ url('admin/contabilidad') }}" class="detailed">CONTABILIDAD</a>
    </li>
    <li class="{{ $pathRequest == 'admin/contents-home'  ? 'active' : '' }}">
      <a href="{{ url('admin/contents-home') }}" class="detailed">BLOG</a>
    </li>
<?php endif ?>
    
</ul>
