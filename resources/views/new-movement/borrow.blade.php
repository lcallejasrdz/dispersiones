@extends("layouts.app")
@section("style")
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
@endsection

@section("wrapper")
<div id="ajax-alert"></div>
<h6 class="mb-0 text-uppercase">Movimiento número: {{ $netx_movement }}</h6>
<hr/>
<form id="form-create" class="row g-3 needs-validation" method="POST" action="{{ route('borrow.store') }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="col-md-4">
        <label for="output-disperser-input" class="form-label">Dispersora *</label>
        <input type="text" class="form-control" id="output-disperser-input" name="disperser_input">
    </div>
    <div class="col-md-4">
        <label for="output-bank-origen-input" class="form-label">Bco/Cta dispersora *</label>
        <input type="text" class="form-control" id="output-bank-origen-input" name="bank_origen_input">
    </div>
    <div class="col-md-4">
        <label for="output-quantity-input" class="form-label">Monto *</label>
        <input type="text" class="form-control money-input" id="output-quantity-input" name="quantity_input">
    </div>
    <div class="col-md-6">
        <label for="output-company-input" class="form-label">Destino *</label>
        <input type="text" class="form-control" id="output-company-input" name="company_input">
    </div>
    <div class="col-md-6">
        <label for="output-bank-destiny-input" class="form-label">Bco/Cta destino *</label>
        <input type="text" class="form-control" id="output-bank-destiny-input" name="bank_destiny_input">
    </div>
    <div class="col-md-12">
        <label for="output-comment-input" class="form-label">Comentario</label>
        <textarea class="form-control" id="output-comment-input" name="comment_input" rows="3"></textarea>
    </div>
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
            var output_company_input = $('#output-company-input').val();
            var output_bank_destiny_input = $('#output-bank-destiny-input').val();
            var output_quantity_input = $('#output-quantity-input').val();
            output_quantity_input = number_format_rollback(output_quantity_input);
            var output_disperser_input = $('#output-disperser-input').val();
            var output_bank_origen_input = $('#output-bank-origen-input').val();

            if(output_company_input == '' || output_bank_destiny_input == '' || (output_quantity_input == '' || isNumber(output_quantity_input) == false) || output_disperser_input == '' || output_bank_origen_input == '')
            {
                var alert = '<div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">';
                    alert += '<div class="d-flex align-items-center">';
                        alert += '<div class="font-35 text-white"><i class="bx bxs-message-square-x"></i>';
                        alert += '</div>';
                        alert += '<div class="ms-3">';
                            alert += '<h6 class="mb-0 text-white">Error</h6>';
                            alert += '<div class="text-white">';
                                alert += '<ul>';
                                    if(output_disperser_input == ''){
                                        alert+= '<li>El campo Dispersora es obligatorio</li>';
                                    }
                                    if(output_bank_origen_input == ''){
                                        alert+= '<li>El campo Bco/Cta Dispersora es obligatorio</li>';
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

                $('#ajax-alert').html(alert);
                $("html, body").animate({ scrollTop: 0 });
            }else{
                $('#output-quantity-input').val(number_format_rollback(output_quantity_input));

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
