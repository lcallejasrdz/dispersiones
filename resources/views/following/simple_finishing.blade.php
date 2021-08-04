@extends("layouts.app")
@section("style")
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
@endsection

@section("wrapper")
<div id="ajax-alert"></div>
<div class="row">
    <div class="col-md-3"><h6 class="mb-0 text-uppercase">Movimiento: {{ $movement->id }}</h6></div>
    <div class="col-md-5"><h6 class="mb-0 text-uppercase">Cliente: {{ $movement->customer }}</h6></div>
    <div class="col-md-4"><h6 class="mb-0 text-uppercase">Monto: ${{ number_format($movement->amount_output, 2) }}</h6></div>
</div>
<hr/>
<div class="col-md-12"><h6 class="mb-0 text-uppercase">Salidas</h6></div>
<hr/>
<form id="form-create" class="row g-3 needs-validation" method="POST" action="{{ route('following.simple_finishing_store', ['id' => $movement->id]) }}" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <table class="table mb-0 table-striped table-bordered" id="entry-table">
        <thead>
            <tr>
                <th>Dispersora</th>
                <th>Bco/Cta dispersora</th>
                <th>Tipo de movimiento</th>
                <th>Monto</th>
                <th>Bco/Cta destino</th>
                <th class="text-center">Comprobante</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outputs as $item)
                <tr>
                    <td>{{ $item->disperser }}</td>
                    <td>{{ $item->bco_cta_disperser }}</td>
                    <td>
                        @if($item->movement_type == 1)
                            Transferencia
                        @elseif($item->movement_type == 2)
                            Efectivo
                        @else
                            Comisión
                        @endif
                    </td>
                    <td class="text-end">${{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->bco_cta_customer }}</td>
                    <td class="text-center">
                        <div class="mb-3">
                            @if($item->receipt == null || $item->receipt == "")
                                <input type="hidden" name="movement_id[]" value="{{ $item->id }}" />
                                <input class="form-control form-control-sm" name="receipt[]" type="file" accept=".jpg,.jpeg,.png,.pdf">
                            @else
                                <div class="font-22"> <i class="lni lni-checkmark"></i></div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-end"><span>${{ number_format($movement->amount_output, 2) }}</span></th>
                <th></th>
                <th class="text-center"></th>
            </tr>
        </tfoot>
    </table>
    <hr/>
    <div class="col-12">
        <button id="submit-btn" class="btn btn-success" type="submit">Registrar Movimiento</button>
    </div>
</form>
@endsection

@section("script")
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/Chart.extension.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
    <script src="{{ asset('assets/js/index.js') }}"></script>

    <script>
        function number_format(amount, decimals) {
            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

            decimals = decimals || 0; // por si la variable no fue fue pasada

            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0) 
                return '$'+parseFloat(0).toFixed(decimals);

            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);

            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;

            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

            amount = amount_parts.join('.');
            return '$'+amount;
        }
    </script>
@endsection
