@extends("layouts.app")
@section("style")
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section("wrapper")
<div id="ajax-alert"></div>
<div class="row">
    <div class="col-md-4">
        <label for="year" class="form-label">Año *</label>
        <select class="form-select" id="year">
            <option selected disabled value>Selecciona una opción...</option>
            @for($i = 2015; $i <= date('Y'); $i++)
                <option value="{{ $i }}" {{ ($i == $year ? "selected":"") }}>{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-4">
        <label for="month" class="form-label">Mes *</label>
        <select class="form-select" id="month">
            <option selected disabled value>Selecciona una opción...</option>
            <option value="1" {{ (1 == $month ? "selected":"") }}>Enero</option>
            <option value="2" {{ (2 == $month ? "selected":"") }}>Febrero</option>
            <option value="3" {{ (3 == $month ? "selected":"") }}>Marzo</option>
            <option value="4" {{ (4 == $month ? "selected":"") }}>Abril</option>
            <option value="5" {{ (5 == $month ? "selected":"") }}>Mayo</option>
            <option value="6" {{ (6 == $month ? "selected":"") }}>Junio</option>
            <option value="7" {{ (7 == $month ? "selected":"") }}>Julio</option>
            <option value="8" {{ (8 == $month ? "selected":"") }}>Agosto</option>
            <option value="9" {{ (9 == $month ? "selected":"") }}>Septiembre</option>
            <option value="10" {{ (10 == $month ? "selected":"") }}>Octubre</option>
            <option value="11" {{ (11 == $month ? "selected":"") }}>Noviembre</option>
            <option value="12" {{ (12 == $month ? "selected":"") }}>Diciembre</option>
        </select>
    </div>
    <div class="col-md-4 text-center">
        <br>
        <button id="date-btn" class="btn btn-success" type="button">Aceptar</button>
    </div>
</div>
<hr>
{{-- Dispersion Movements --}}
<h6 class="mb-0 text-uppercase">Finales</h6>
<hr/>
<table class="table table-striped table-bordered" style="width:100%" id="final_movements">
    <thead>
        <tr>
            <th>Movimiento</th>
            <th>Fecha / Hora</th>
            <th>Cliente</th>
            <th>Tipo de Movimiento</th>
            <th>Monto</th>
            <th>Bco/Cta Cliente</th>
            <th>Comprobante</th>
        </tr>
    </thead>
    <tbody>
            @foreach($final_movements as $item)
                <tr>
                    <td>{{ $item->movement_id }}</td>
                    <td>{{ date('d/m/Y H:i:s', strtotime($item->created_at)) }}</td>
                    <td>{{ $item->customer }}</td>
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
                    <td class="">{{ $item->bco_cta_customer }}</td>
                    <td class="text-center">
                        <a href="{{ url('/uploads/final_receipts/'.$item->receipt) }}" target="_blank">
                            <div class="d-flex align-items-center">
                                <div><i class="bx bxs-file-pdf me-2 font-24 text-danger"></i>
                                </div>
                                <div class="font-weight-bold text-danger">Comprobante</div>
                            </div>
                        </a>
                    </td>
                </tr>
            @endforeach
    </tbody>
</table>
@endsection

@section("script")
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#final_movements').DataTable();
        });

        $('#date-btn').click(function(){
            var year = $('#year').val();
            var month = $('#month').val();

            if(year == '' || month == '')
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert += '<ul>';
                                    if(year == ''){
                                        alert += '<li>El campo Año en es obligatorio</li>';
                                    }
                                    if(month == ''){
                                        alert += '<li>El campo Mes en es obligatorio</li>';
                                    }
                                alert += '</ul>';
                            alert += '</div>';
                        alert += '</div>';
                    alert += '</div>';
                    alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                alert += '</div>';

                $('#ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: 0 });
            }
            else
            {
                document.location.href="/list/finals/"+year+"/"+month;
            }
        });

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
