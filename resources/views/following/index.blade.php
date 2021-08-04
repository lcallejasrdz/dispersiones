@extends("layouts.app")
@section("style")
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section("wrapper")
{{-- Direct Movements --}}
<h6 class="mb-0 text-uppercase">Directos</h6>
<hr/>
<table class="table table-striped table-bordered" style="width:100%" id="direct_movements">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha / Hora</th>
            <th>Cliente</th>
            <th>Monto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($direct_movements as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ date('d/m/Y H:i:s', strtotime($item->created_at)) }}</td>
                <td>{{ $item->customer }}</td>
                <td class="text-end">${{ number_format($item->amount_output, 2) }}</td>
                <td class="text-center">
                    @if($item->status <= 4)
                        <a class="btn btn-primary" href="{{ route('following.direct_following_create', ['id' => $item->id]) }}">Detalles</a>
                    @endif

                    @if($item->status == 5)
                        <a class="btn btn-success" href="{{ route('following.direct_finishing_create', ['id' => $item->id]) }}">Finalizar</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<hr/>
{{-- Payroll Movements --}}
<h6 class="mb-0 text-uppercase">Nóminas</h6>
<hr/>
<table class="table table-striped table-bordered" style="width:100%" id="payroll_movements">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha / Hora</th>
            <th>Cliente</th>
            <th>Monto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payroll_movements as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ date('d/m/Y H:i:s', strtotime($item->created_at)) }}</td>
                <td>{{ $item->customer }}</td>
                <td class="text-end">${{ number_format($item->amount_entry, 2) }}</td>
                <td class="text-center">
                    <a class="btn btn-primary" href="{{ route('following.payroll_following_create', ['id' => $item->id]) }}">Detalles</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<hr/>
{{-- Simple Movements --}}
<h6 class="mb-0 text-uppercase">Simples</h6>
<hr/>
<table class="table table-striped table-bordered" style="width:100%" id="simple_movements">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha / Hora</th>
            <th>Cliente</th>
            <th>Monto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($simple_movements as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ date('d/m/Y H:i:s', strtotime($item->created_at)) }}</td>
                <td>{{ $item->customer }}</td>
                <td class="text-end">${{ number_format($item->amount_output, 2) }}</td>
                <td class="text-center">
                    @if($item->status <= 4)
                        <a class="btn btn-primary" href="{{ route('following.simple_following_create', ['id' => $item->id]) }}">Detalles</a>
                    @endif

                    @if($item->status == 5)
                        <a class="btn btn-success" href="{{ route('following.simple_finishing_create', ['id' => $item->id]) }}">Finalizar</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<hr/>
{{-- Borrow Movements --}}
<h6 class="mb-0 text-uppercase">Prestamos</h6>
<hr/>
<table class="table table-striped table-bordered" style="width:100%" id="borrow_movements">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha / Hora</th>
            <th>Cliente</th>
            <th>Monto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($borrow_movements as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ date('d/m/Y H:i:s', strtotime($item->created_at)) }}</td>
                <td>{{ $item->customer }}</td>
                <td class="text-end">${{ number_format($item->amount_output, 2) }}</td>
                <td class="text-center">
                    <a class="btn btn-success" href="{{ route('following.borrow_finishing_create', ['id' => $item->id]) }}">Finalizar</a>
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
            $('#direct_movements').DataTable();
            $('#payroll_movements').DataTable();
            $('#simple_movements').DataTable();
            $('#borrow_movements').DataTable();
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
