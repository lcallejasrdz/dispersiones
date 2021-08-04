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
<div class="col-md-12"><h6 class="mb-0 text-uppercase">Entradas</h6></div>
<hr/>
<table class="table mb-0 table-striped table-bordered" id="entry-table">
    <thead>
        <tr>
            <th>Depositó en</th>
            <th>Monto</th>
            <th>Banco</th>
            <th>Cuenta</th>
            <th>Confirmar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entries as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-end">${{ number_format($item->amount, 2) }}</td>
                <td>{{ $item->bank }}</td>
                <td>{{ $item->account }}</td>
                <td class="text-center">
                    @if($item->confirm == 1)
                        <div class="font-22"> <i class="lni lni-checkmark"></i></div>
                    @else
                        <button type="button" class="btn btn-success" onClick="confirmEntry(this, {{ $item->id }})">Confirmar</button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            @if($entries->count() > 0)
                <th class="text-end"><span>${{ number_format($movement->amount_output, 2) }}</span></th>
            @else
                <th class="text-end"><span>$0.00</span></th>
            @endif
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<hr/>
<div class="col-md-12"><h6 class="mb-0 text-uppercase">Dispersiones</h6></div>
<hr/>
@if(count($dispersions) > 0)
    <table class="table mb-0 table-striped table-bordered" id="entry-table">
        <thead>
            <tr>
                <th>Dispersora</th>
                <th>Bco/Cta Dispersora</th>
                <th>Monto</th>
                <th>Destino</th>
                <th>Bco/Cta Destino</th>
                <th>Cuenta Final</th>
                @if($movement->status == 4)
                    <th class="text-center">Confirmar</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($dispersions as $item)
                <tr>
                    <td>{{ $item->disperser_name }}</td>
                    <td>{{ $item->disperser_bank_account }}</td>
                    <td class="text-end">${{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->destiny_name }}</td>
                    <td>{{ $item->destiny_bank_account }}</td>
                    <td class="text-center">
                        @if($item->final_account == 1)
                            <div class="font-22"> <i class="lni lni-checkmark"></i></div>
                        @endif
                    </td>
                    @if($movement->status == 4)
                        <td class="text-center">
                            @if($item->confirm == 1)
                                <div class="font-22"> <i class="lni lni-checkmark"></i></div>
                            @else
                                <button type="button" class="btn btn-success" onClick="confirmDispersion(this, {{ $item->id }})">Confirmar</button>
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th class="text-end"><span>${{ number_format($movement->amount_output, 2) }}</span></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@else
    <form id="form-create" class="row g-3 needs-validation" method="POST" action="{{ route('following.simple_following_store', ['id' => $movement->id]) }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" id="output_total" value="{{ $movement->amount_output }}">
        <div id="dispersion-ajax-alert"></div>
        <div class="col-md-4">
            <label for="disperser-input" class="form-label">Dispersora *</label>
            <select class="form-select" id="disperser-input">
                <option selected disabled value>Selecciona una opción...</option>
                @foreach($companies as $company)
                    <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="disperser-bank-input" class="form-label">Bco/Cta Dispersora *</label>
            <input type="text" class="form-control" id="disperser-bank-input">
        </div>
        <div class="col-md-4">
            <label for="quantity-input" class="form-label">Monto *</label>
            <input type="text" class="form-control money-input" id="quantity-input">
        </div>
        <div class="col-md-4">
            <label for="destiny-input" class="form-label">Destino *</label>
            <select class="form-select" id="destiny-input">
                <option selected disabled value>Selecciona una opción...</option>
                @foreach($companies as $company)
                    <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="destiny-bank-input" class="form-label">Bco/Cta Destino *</label>
            <input type="text" class="form-control" id="destiny-bank-input">
        </div>
        <div class="col-md-4">
            <br>
            <input class="form-check-input" type="checkbox" value="" id="final-account-input">
            <label class="form-check-label" for="final-account-input">Cuenta Final</label>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="button" id="add-dispersion">Agregar</button>
        </div>
        <table class="table mb-0 table-striped table-bordered" id="dispersion-table">
            <thead>
                <tr>
                    <th>Dispersora</th>
                    <th>Bco/Cta Dispersora</th>
                    <th>Monto</th>
                    <th>Destino</th>
                    <th>Bco/Cta Destino</th>
                    <th>Cuenta Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th class="text-end"><input type="hidden" name="dispersion_total" id="dispersion_total"><span id="dispersion-table-total">$0.00</span></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <hr/>
        <div class="col-12">
            <button id="submit-btn" class="btn btn-success" type="button">Registrar Movimiento</button>
        </div>
    </form>
@endif
<hr/>
<div class="col-md-12"><h6 class="mb-0 text-uppercase">Salidas</h6></div>
<hr/>
<table class="table mb-0 table-striped table-bordered" id="entry-table">
    <thead>
        <tr>
            <th>Dispersora</th>
            <th>Bco/Cta dispersora</th>
            <th>Tipo de movimiento</th>
            <th>Monto</th>
            <th>Destino</th>
            <th>Bco/Cta destino</th>
            <th>Comentario</th>
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
                <td>{{ $item->customer }}</td>
                <td>{{ $item->bco_cta_customer }}</td>
                <td>{{ $item->comment }}</td>
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
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
@endsection

@section("script")
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/Chart.extension.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
    <script src="{{ asset('assets/js/index.js') }}"></script>

    <script>
        $('#add-dispersion').click(function(){
            var disperser_input = $('#disperser-input').val();
            var disperser_input_text = $('#disperser-input option:selected').text();
            var disperser_bank_input = $('#disperser-bank-input').val();
            var quantity_input = $('#quantity-input').val();
            quantity_input = number_format_rollback(quantity_input);
            var destiny_input = $('#destiny-input').val();
            var destiny_input_text = $('#destiny-input option:selected').text();
            var destiny_bank_input = $('#destiny-bank-input').val();
            var final_account_input = $('#final-account-input:checked').length;

            var dispersion_table_total = $('#dispersion-table-total').html();
            dispersion_table_total = number_format_rollback(dispersion_table_total);
            if(dispersion_table_total == '' || dispersion_table_total == null){
                dispersion_table_total = 0;
            }

            if((disperser_input == '' || disperser_input == null) || disperser_bank_input == '' || (quantity_input == '' || isNumber(quantity_input) == false) || (destiny_input == '' || destiny_input == null) || destiny_bank_input == '')
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert += '<ul>';
                                    if(disperser_input == '' || disperser_input == null){
                                        alert += '<li>El campo Dispersora es obligatorio</li>';
                                    }
                                    if(disperser_bank_input == ''){
                                        alert += '<li>El campo Bco/Cta Dispersora es obligatorio</li>';
                                    }

                                    if(quantity_input == ''){
                                        alert += '<li>El campo Monto es obligatorio</li>';
                                    }else if(isNumber(quantity_input) == false){
                                        alert += '<li>El campo Monto debe ser numérico</li>';
                                    }

                                    if(destiny_input == '' || destiny_input == null){
                                        alert += '<li>El campo Destino es obligatorio</li>';
                                    }
                                    if(destiny_bank_input == ''){
                                        alert += '<li>El campo Bco/Cta Destino es obligatorio</li>';
                                    }
                                alert += '</ul>';
                            alert += '</div>';
                        alert += '</div>';
                    alert += '</div>';
                    alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                alert += '</div>';

                $('#dispersion-ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: $("#dispersion-ajax-alert").offset().top - 85 });
            }
            else
            {
                $('#dispersion-ajax-alert').html('');
                $('#disperser-input').val('');
                $('#disperser-bank-input').val('');
                $('#quantity-input').val('');
                $('#destiny-input').val('');
                $('#destiny-bank-input').val('');
                $('#final-account-input').prop( "checked", false );

                //pasar los valores a la tabla
                var rows = $('#dispersion-table tbody').html();

                rows += '<tr>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="disperser[]" value="'+ disperser_input +'" />';
                        rows += disperser_input_text;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="disperser_bank[]" value="'+ disperser_bank_input +'" />';
                        rows += disperser_bank_input;
                    rows += '</td>';
                    rows += '<td class="text-end">';
                        rows += '<input type="hidden" name="quantity[]" value="'+ number_format_rollback(number_format(quantity_input, 2)) +'" />';
                        rows += number_format(quantity_input, 2);
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="destiny[]" value="'+ destiny_input +'" />';
                        rows += destiny_input_text;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="destiny_bank[]" value="'+ destiny_bank_input +'" />';
                        rows += disperser_bank_input;
                    rows += '</td>';
                    rows += '<td class="text-center">';
                        rows += '<input type="hidden" name="final_account[]" value="'+ final_account_input +'" />';
                        if(final_account_input == 1){
                            rows += '<div class="font-22"> <i class="lni lni-checkmark"></i></div>';
                        }
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<button type="button" class="btn btn-danger" onClick="deleteRowDispersion(this, '+ quantity_input +', '+ final_account_input +')">Eliminar</button>';
                    rows += '</td>';
                rows += '</tr>';

                $('#dispersion-table tbody').html(rows);
                if(final_account_input == 1){
                    var total = parseFloat(dispersion_table_total) + parseFloat(quantity_input);
                    $('#dispersion_total').val(number_format_rollback(number_format(total, 2)));
                }else{
                    var total = parseFloat(dispersion_table_total);
                }
                $('#dispersion-table-total').html(number_format(total, 2));
            }
        });

        function deleteRowDispersion(btn, amount, disperser){
            var dispersion_table_total = $('#dispersion-table-total').html();
            dispersion_table_total = number_format_rollback(dispersion_table_total);

            if(disperser == 1){
                var total = parseFloat(dispersion_table_total) - parseFloat(amount);
            }else{
                var total = parseFloat(dispersion_table_total);
            }

            $('#dispersion_total').val(number_format_rollback(number_format(total, 2)));
            $('#dispersion-table-total').html(number_format(total, 2));

            btn.closest('tr').remove();
        }

        $(".money-input").on({
            "focus": function (event) {
                $(event.target).select();
            },
            "keyup": function (event) {
                $(event.target).val(function (index, value ) {
                    return value.replace(/\D/g, "")
                                .replace(/([0-9])([0-9]{2})$/, '$1.$2')
                                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ",");
                });
            }
        });

        $('#submit-btn').click(function(){
            var dispersion_total = $('#dispersion_total').val();

            if(dispersion_total == 0 || dispersion_total == '' || dispersion_total == null)
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert += '<ul>';
                                    alert += '<li>El monto total de dispersiones no puede ser 0</li>';
                                alert+= '</ul>';
                            alert += '</div>';
                        alert += '</div>';
                    alert += '</div>';
                    alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                alert += '</div>';

                $('#dispersion-ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: $("#dispersion-ajax-alert").offset().top - 85 });
            }else{
                $('#form-create').submit();
            }
        });

        function isNumber(n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        }

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

        function number_format_rollback(amount) {
            amount = amount.replace(",", "");
            amount = amount.replace("$", "");

            return amount;
        }

        function confirmEntry(btn, id){
            $.get('/following/confirm_entry/'+id, function(data){
                if(data == 1){
                    var check = '<div class="font-22"> <i class="lni lni-checkmark"></i></div>';
                    $(btn).closest('td').html(check);
                    btn.remove();
                }else if(data == 2){
                    document.location.reload();
                }else{
                    var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                        alert += '<div class="d-flex align-items-center">';
                            alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                            alert += '</div>';
                            alert += '<div class="ms-3">';
                                alert += '<h6 class="mb-0 text-white">Error</h6>';
                                alert += '<div class="text-white">';
                                    alert += '<ul>';
                                        alert += '<li>Error al confirmar el movimiento, intentalo más tarde o contacta a soporte</li>';
                                    alert+= '</ul>';
                                alert += '</div>';
                            alert += '</div>';
                        alert += '</div>';
                        alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    alert += '</div>';

                    $('#ajax-alert').html(alert);
                    $("html, body").animate({ scrollTop: 0 });
                }
            });
        }

        function confirmDispersion(btn, id){
            $.get('/following/confirm_dispersion/'+id, function(data){
                if(data == 1){
                    var check = '<div class="font-22"> <i class="lni lni-checkmark"></i></div>';
                    $(btn).closest('td').html(check);
                    btn.remove();
                }else if(data == 2){
                    document.location.href="/following";
                }else{
                    var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                        alert += '<div class="d-flex align-items-center">';
                            alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                            alert += '</div>';
                            alert += '<div class="ms-3">';
                                alert += '<h6 class="mb-0 text-white">Error</h6>';
                                alert += '<div class="text-white">';
                                    alert += '<ul>';
                                        alert += '<li>Error al confirmar la dispersión, intentalo más tarde o contacta a soporte</li>';
                                    alert+= '</ul>';
                                alert += '</div>';
                            alert += '</div>';
                        alert += '</div>';
                        alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    alert += '</div>';

                    $('#dispersion-ajax-alert').html(alert);
                    $("html, body").animate({ scrollTop: $("#dispersion-ajax-alert").offset().top - 85 });
                }
            });
        }
    </script>
@endsection
