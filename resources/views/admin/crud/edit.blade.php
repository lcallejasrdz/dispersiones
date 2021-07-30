@extends("layouts.app")
@section("style")
@endsection

@section("wrapper")
    <form method="POST" action="{{ route('companies.update', ['id' => $item->slug]) }}" class="row g-3 needs-validation" novalidate>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PUT">
        <div class="col-md-12">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ $item->name }}" required>
            <div class="invalid-feedback">Por favor ingresa un nuevo nombre válido.</div>
        </div>
        <hr/>
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
    </form>
@endsection

@section("script")
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
            (function () {
              'use strict'

              // Fetch all the forms we want to apply custom Bootstrap validation styles to
              var forms = document.querySelectorAll('.needs-validation')

              // Loop over them and prevent submission
              Array.prototype.slice.call(forms)
                .forEach(function (form) {
                  form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                      event.preventDefault()
                      event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                  }, false)
                })
            })()
    </script>
@endsection
