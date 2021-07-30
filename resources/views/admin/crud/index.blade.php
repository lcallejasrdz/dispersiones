@extends("layouts.app")
@section("style")
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section("wrapper")
    <div class="table-responsive">
        <table id="companies" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <a class="btn btn-success" href="{{ route('companies.show', ['id' => $item->slug]) }}">Mostrar</a>
                            <a class="btn btn-primary" href="{{ route('companies.edit', ['id' => $item->slug]) }}">Editar</a>
                            <button onClick="DeleteShow('{{ $item->slug }}', '{{ $item->name }}');" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDangerModal">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tfoot>
        </table>
    </div>
@endsection

@section("components")
    <div class="modal fade" id="deleteDangerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-danger">
                <form method="POST" action="{{ route('companies.delete') }}" class="row g-3 needs-validation" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title text-white">Eliminar registro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-white">
                        <p class="text-center">Confirma que deseas borrar el registro con nombre:</p>
                        <h4 class="text-center text-light"><strong><span id="modalName"></span></strong></h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="slug" id="modalDelete" value="">
                        <button class="btn btn-dark" type="submit">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section("script")
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#companies').DataTable();
        });
        function DeleteShow(slug, name){
            $('#modalName').text(name);
            $('#modalDelete').val(slug);
        }
    </script>
@endsection
