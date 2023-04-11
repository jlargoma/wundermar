<br/>
<header id="header" class="static-sticky transparent-header  not-dark " style="display: none;z-index: 999 !important;">

  <div id="header-wrap">
    <div id="primary-menu-trigger"><i class="fa fa-bars"></i></div>

    <!-- Primary Navigation
    ============================================= -->
    <nav id="primary-menu" class="with-arrows style-2 center">

      <ul>
        <?php if (Request::path() != '/'): ?>
          <li>
            <a  href="{{ url('/') }}"><div style="text-align: center; font-size: 18px;"><i class="fa fa-home fa-2x" style="margin-right: 0;font-size: 20px!important"></i> </div></a></li>
          </li>
        <?php endif ?>
        <li class="mega-menu">
          <a href="#"><div>{{ $site_id == 2 ? 'habitaciones' : 'Apartamentos' }}</div></a>
          <div class="mega-menu-content style-2 clearfix">
            <ul class="mega-menu-column">
            @if(count($roomsUrl)>0)
            @foreach($roomsUrl as $item)
            <li class="mega-menu-title">
              <a class="font-w600" href="{{$item['u']}}"><div>{{$item['t']}}</div></a>
            </li>
            @endforeach
            @endif
            </ul>
          </div>
        </li>
        <!-- <li>
                <a href="{{ url('/reserva') }}"><div>Reserva</div></a></li>
        </li> -->
        <li>
          @if($site_id == 2)
          <a href="/el-hotel"><div>El Hotel</div></a></li>
          @else
          <a href="{{ route('web.edificio') }}"><div>El Edificio</div></a></li>
          @endif
        </li>
        <li>
          <a href="{{ url('/blog') }}">
            <div>{{ $site_id == 2 ? 'Experiencias' : '¿Qué hacer en sierra nevada?' }}</div>
          </a>
        </li>

        <li >
          @if(Request::path() == 'contacto')
           <a href="/"><div>Reservar</div></a> 
          @else
          <a class="menu-booking showFormBook" href="#" data-href="#wrapper"><div>Reservar</div></a>
          @endif
        </li>
        <li>
          <a href="{{ url('/contacto') }}"><div>Contacto</div></a></li>
        </li>
      </ul>

    </nav><!-- #primary-menu end -->

  </div>
</header>
