<div class="table-responsive">
    <table class="table table-vta-agenc">
        <thead>
            <tr>
                <th>Agencia</th>
                <th>VTAS</th>
                <th>VTAS %</th>
                <th>RES.</th>
                <th>RES.%</th>
                <th>COMISIÓN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataSeason as $k=>$v)
            <tr>
                <td>{{show_isset($agencyBooks,$k)}}</td>
                <td>{{$v['total']}}</td>
                <td>{{$v['total_rate']}}</td>
                <td>{{$v['reservations']}}</td>
                <td>{{$v['reservations_rate']}}</td>
                <td>{{$v['commissions']}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th>{{moneda($totalSeason['total'])}}</th>
                <th></th>
                <th>{{($totalSeason['reservations'])}}</th>
                <th></th>
                <th>{{moneda($totalSeason['commissions'])}}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>