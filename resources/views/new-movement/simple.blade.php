@extends("layouts.app")
@section("style")
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
@endsection

@section("wrapper")
<div id="ajax-alert"></div>
<h6 class="mb-0 text-uppercase">Movimiento número: {{ $netx_movement }}</h6>
<hr/>
<form id="form-create" class="row g-3 needs-validation" method="POST" action="{{ route('simple.store') }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="col-md-12">
        <label for="customer" class="form-label">Cliente</label>
        <input type="text" class="form-control" name="customer" id="customer" required>
    </div>
    <hr/>
    <div id="entry-ajax-alert"></div>
    <div class="col-md-3">
        <label for="entry-to-input" class="form-label">Depositó en *</label>
        <select class="form-select" id="entry-to-input">
            <option selected disabled value>Selecciona una opción...</option>
            @foreach($companies as $company)
                <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="entry-quantity-input" class="form-label">Monto *</label>
        <input type="text" class="form-control money-input" id="entry-quantity-input">
    </div>
    <div class="col-md-3">
        <label for="entry-bank-input" class="form-label">Banco *</label>
        <input type="text" class="form-control" id="entry-bank-input">
    </div>
    <div class="col-md-3">
        <label for="entry-account-input" class="form-label">Cuenta</label>
        <input type="text" class="form-control" id="entry-account-input">
    </div>
    <div class="col-12">
        <button class="btn btn-primary" type="button" id="add-entry">Agregar</button>
    </div>
    <table class="table mb-0 table-striped table-bordered" id="entry-table">
        <thead>
            <tr>
                <th>Depositó en</th>
                <th>Monto</th>
                <th>Banco</th>
                <th>Cuenta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th></th>
                <th class="text-end"><input type="hidden" name="entry_total" id="entry_total"><span id="entry-table-total">$0.00</span></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <hr/>
    <div id="output-ajax-alert"></div>
    <div class="col-md-4">
        <label for="output-bank-origen-input" class="form-label">Bco/Cta dispersora *</label>
        <input type="text" class="form-control" id="output-bank-origen-input">
    </div>
    <div class="col-md-4">
        <label for="output-type-input" class="form-label">Tipo de movimiento *</label>
        <select class="form-select" id="output-type-input">
            <option selected disabled value>Selecciona una opción...</option>
            <option value="1">Transferencia</option>
            <option value="2">Efectivo</option>
            <option value="3">Comisión</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="output-quantity-input" class="form-label">Monto *</label>
        <input type="text" class="form-control money-input" id="output-quantity-input">
    </div>
    <div class="col-md-6">
        <label for="output-company-input" class="form-label">Destino *</label>
        <input type="text" class="form-control" id="output-company-input">
    </div>
    <div class="col-md-6">
        <label for="output-bank-destiny-input" class="form-label">Bco/Cta destino *</label>
        <input type="text" class="form-control" id="output-bank-destiny-input">
    </div>
    <div class="col-md-12">
        <label for="output-comment-input" class="form-label">Comentario</label>
        <textarea class="form-control" id="output-comment-input" rows="3"></textarea>
    </div>
    <div class="col-12">
        <button class="btn btn-primary" type="button" id="add-output">Agregar</button>
    </div>
    <table class="table mb-0 table-striped table-bordered" id="output-table">
        <thead>
            <tr>
                <th>Bco/Cta dispersora</th>
                <th>Tipo de movimiento</th>
                <th>Monto</th>
                <th>Destino</th>
                <th>Bco/Cta destino</th>
                <th>Comentario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th class="text-end"><input type="hidden" name="output_total" id="output_total"><span id="output-table-total">$0.00</span></th>
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
@endsection

@section("script")
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/Chart.extension.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
    <script src="{{ asset('assets/js/index.js') }}"></script>

    <script>
        $('#add-entry').click(function(){
            var entry_to_input = $('#entry-to-input').val();
            var entry_to_input_text = $('#entry-to-input option:selected').text();
            var entry_quantity_input = $('#entry-quantity-input').val();
            entry_quantity_input = number_format_rollback(entry_quantity_input);
            var entry_bank_input = $('#entry-bank-input').val();
            var entry_account_input = $('#entry-account-input').val();

            var entry_table_total = $('#entry-table-total').html();
            entry_table_total = number_format_rollback(entry_table_total);
            if(entry_table_total == '' || entry_table_total == null){
                entry_table_total = 0;
            }

            if((entry_to_input == '' || entry_to_input == null) || (entry_quantity_input == '' || isNumber(entry_quantity_input) == false) || entry_bank_input == '')
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert += '<ul>';
                                    if(entry_to_input == ''|| entry_to_input == null){
                                        alert += '<li>El campo Depósito en es obligatorio</li>';
                                    }

                                    if(entry_quantity_input == ''){
                                        alert += '<li>El campo Monto es obligatorio</li>';
                                    }else if(isNumber(entry_quantity_input) == false){
                                        alert += '<li>El campo Monto debe ser numérico</li>';
                                    }

                                    if(entry_bank_input == ''){
                                        alert += '<li>El campo Banco es obligatorio</li>';
                                    }
                                alert += '</ul>';
                            alert += '</div>';
                        alert += '</div>';
                    alert += '</div>';
                    alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                alert += '</div>';

                $('#entry-ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: $("#entry-ajax-alert").offset().top - 85 });
            }
            else
            {
                $('#entry-ajax-alert').html('');
                $('#entry-to-input').val('');
                $('#entry-quantity-input').val('');
                $('#entry-bank-input').val('');
                $('#entry-account-input').val('');

                //pasar los valores a la tabla
                var rows = $('#entry-table tbody').html();

                rows += '<tr>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="entry_company[]" value="'+ entry_to_input +'" />';
                        rows += entry_to_input_text;
                    rows += '</td>';
                    rows += '<td class="text-end">';
                        rows += '<input type="hidden" name="entry_quantity[]" value="'+ number_format_rollback(number_format(entry_quantity_input, 2)) +'" />';
                        rows += number_format(entry_quantity_input, 2);
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="entry_bank[]" value="'+ entry_bank_input +'" />';
                        rows += entry_bank_input;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="entry_account[]" value="'+ entry_account_input +'" />';
                        rows += entry_account_input;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<button type="button" class="btn btn-danger" onClick="deleteRowEntry(this, '+ entry_quantity_input +')">Eliminar</button>';
                    rows += '</td>';
                rows += '</tr>';

                $('#entry-table tbody').html(rows);
                var total = parseFloat(entry_table_total) + parseFloat(entry_quantity_input);
                $('#entry_total').val(number_format_rollback(number_format(total, 2)));
                $('#entry-table-total').html(number_format(total, 2));
            }
        });

        $('#add-output').click(function(){
            var output_company_input = $('#output-company-input').val();
            var output_bank_destiny_input = $('#output-bank-destiny-input').val();
            var output_type_input = $('#output-type-input option:selected').val();
            var output_type_input_text = $('#output-type-input option:selected').text();
            var output_quantity_input = $('#output-quantity-input').val();
            output_quantity_input = number_format_rollback(output_quantity_input);
            var output_bank_origen_input = $('#output-bank-origen-input').val();
            var output_comment_input = $('#output-comment-input').val();

            var output_table_total = $('#output-table-total').html();
            output_table_total = number_format_rollback(output_table_total);
            if(output_table_total == '' || output_table_total == null){
                output_table_total = 0;
            }

            if(output_company_input == '' || output_bank_destiny_input == '' || (output_type_input == '' || output_type_input == null) || (output_quantity_input == '' || isNumber(output_quantity_input) == false) || output_bank_origen_input == '')
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert+= '<ul>';
                                    if(output_bank_origen_input == ''){
                                        alert+= '<li>El campo Bco/Cta Dispersora es obligatorio</li>';
                                    }
                                    if(output_type_input == '' || output_type_input == null){
                                        alert+= '<li>El campo Tipo de movimiento es obligatorio</li>';
                                    }
                                    if(output_quantity_input == ''){
                                        alert+= '<li>El campo Monto es obligatorio</li>';
                                    }else if(isNumber(output_quantity_input) == false){
                                        alert+= '<li>El campo Monto debe ser numérico</li>';
                                    }
                                    if(output_company_input == ''){
                                        alert+= '<li>El campo Destino es obligatorio</li>';
                                    }
                                    if(output_bank_destiny_input == ''){
                                        alert+= '<li>El campo Bco/Cta destino es obligatorio</li>';
                                    }
                                alert+= '</ul>';
                            alert += '</div>';
                        alert += '</div>';
                    alert += '</div>';
                    alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                alert += '</div>';

                $('#output-ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: $("#output-ajax-alert").offset().top - 85 });
            }
            else
            {
                $('#output-ajax-alert').html('');
                $('#output-company-input').val('');
                $('#output-bank-destiny-input').val('');
                $('#output-type-input').val('');
                $('#output-quantity-input').val('');
                $('#output-bank-origen-input').val('');
                $('#output-comment-input').val('');

                //pasar los valores a la tabla
                var rows = $('#output-table tbody').html();

                rows += '<tr>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="output_bank_origen[]" value="'+ output_bank_origen_input +'" />';
                        rows += output_bank_origen_input;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="output_type[]" value="'+ output_type_input +'" />';
                        rows += output_type_input_text;
                    rows += '</td>';
                    rows += '<td class="text-end">';
                        rows += '<input type="hidden" name="output_quantity[]" value="'+ number_format_rollback(number_format(output_quantity_input, 2)) +'" />';
                        rows += number_format(output_quantity_input, 2);
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="output_company[]" value="'+ output_company_input +'" />';
                        rows += output_company_input;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="output_bank_destiny[]" value="'+ output_bank_destiny_input +'" />';
                        rows += output_bank_destiny_input;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<input type="hidden" name="output_comment[]" value="'+ output_comment_input +'" />';
                        rows += output_comment_input;
                    rows += '</td>';
                    rows += '<td>';
                        rows += '<button type="button" class="btn btn-danger" onClick="deleteRowOutput(this, '+ output_quantity_input +')">Eliminar</button>';
                    rows += '</td>';
                rows += '</tr>';

                $('#output-table tbody').html(rows);
                var total = parseFloat(output_table_total) + parseFloat(output_quantity_input);
                $('#output_total').val(number_format_rollback(number_format(total, 2)));
                $('#output-table-total').html(number_format(total, 2));
            }
        });

        function deleteRowEntry(btn, amount){
            var entry_table_total = $('#entry-table-total').html();
            entry_table_total = number_format_rollback(entry_table_total);

            var total = parseFloat(entry_table_total) - parseFloat(amount);
            $('#entry_total').val(number_format_rollback(number_format(total, 2)));
            $('#entry-table-total').html(number_format(total, 2));

            btn.closest('tr').remove();
        }

        function deleteRowOutput(btn, amount){
            var output_table_total = $('#output-table-total').html();
            output_table_total = number_format_rollback(output_table_total);

            var total = parseFloat(output_table_total) - parseFloat(amount);
            $('#output_total').val(total);
            $('#output-table-total').html(number_format(total, 2));

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
            var customer = $('#customer').val();
            var entry_total = $('#entry_total').val();
            var output_total = $('#output_total').val();

            if(customer == '' || (output_total == 0 || output_total == '' || output_total == null))
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert += '<ul>';
                                    if(customer == ''){
                                        alert+= '<li>El campo cliente es obligatorio</li>';
                                    }

                                    if(output_total == 0 || output_total == '' || output_total == null){
                                        alert += '<li>El monto total de salida no puede ser 0</li>';
                                    }
                                alert+= '</ul>';
                            alert += '</div>';
                        alert += '</div>';
                    alert += '</div>';
                    alert += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                alert += '</div>';

                $('#ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: 0 });
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
    </script>
@endsection
