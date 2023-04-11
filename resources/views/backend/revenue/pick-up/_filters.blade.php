<form id="revenu_filters" method="get" action="{{route('revenue.pickUp')}}" style="display: inline-block">
  <input type="hidden" id="sel_mes" name="sel_mes" value="{{$sel_mes}}">
  <div class="filter-field">
    <label>Edificio</label>
    <select name="site" id="site" class="form-control">
      <option value="">Todos</option>
      <?php foreach (\App\Sites::all() as $item): ?>
        <option value="<?php echo $item->id ?>" @if($item->id == $site) selected @endif><?php echo $item->name ?></option>
      <?php endforeach ?>
    </select>
  </div>
    <input type="hidden" id="ch_sel" name="ch_sel" value="{{$ch_sel}}">
</form>
<div class="clearfix"></div>
<div class="filters-box">
<div class="filters-lst">
<span class="tabChannels @if(!$ch_sel) active @endif" data-k="">
  TODOS
</span>
@foreach($channels as $ch)
<span class="tabChannels @if($ch_sel == $ch) active @endif"  data-k="{{$ch}}">
  {{$ch}}
</span>
@endforeach
</div>
</div>
<style>
  .filter-field{
    max-width: 120px;
  }
  @media only screen and (max-width: 780px) {
    .filters-box{
      max-width: 98%;
      overflow: auto;
    }
    .filters-lst{
      width: 100em;
    }
  }
</style>