@extends("layouts.app")
@section("style")
@endsection

@section("wrapper")
    <table class="table mb-0 table-striped table-bordered">
        <tbody>
            <tr>
                <th>ID</th>
                <td>{{ $item->id }}</td>
            </tr>
            <tr>
                <th>Slug</th>
                <td>{{ $item->slug }}</td>
            </tr>
            <tr>
                <th>Nombre</th>
                <td>{{ $item->name }}</td>
            </tr>
        </tbody>
    </table>
@endsection

@section("script")
@endsection
