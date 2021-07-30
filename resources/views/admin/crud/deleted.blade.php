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
                            <form method="POST" action="{{ route('companies.restore', ['id' => $item->slug]) }}" class="row g-3 needs-validation" novalidate>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-warning" type="submit">Restaurar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tfoot>
        </table>
    </div>
@endsection

@section("script")
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#companies').DataTable();
          } );
    </script>
@endsection
