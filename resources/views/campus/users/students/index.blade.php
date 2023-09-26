@extends('adminlte::page')

@section('title', __('users.students.title'))

@section('content_header')
    <h1>{{ __('users.students.header', ["campus" => $campus->Nombre]) }}</h1>
@stop

@section('content')
    <div class="card">
        <div id="wrapper" class="card-body">
            <table id="students" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('users.students.table.headers.code') }}</th>
                        <th>{{ __('users.students.table.headers.first_name') }}</th>
                        <th>{{ __('users.students.table.headers.last_name') }}</th>
                        <th>{{ __('users.students.table.headers.ci') }}</th>
                        <th>{{ __('users.students.table.headers.email') }}</th>
                        <th>{{ __('users.students.table.headers.phone') }}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>{{ __('users.students.table.headers.code') }}</th>
                        <th>{{ __('users.students.table.headers.first_name') }}</th>
                        <th>{{ __('users.students.table.headers.last_name') }}</th>
                        <th>{{ __('users.students.table.headers.ci') }}</th>
                        <th>{{ __('users.students.table.headers.email') }}</th>
                        <th>{{ __('users.students.table.headers.phone') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
@stop

@section('js')
    <script src="{{ asset('js/functions.js') }}"></script>
    <script>
        $(function () {
            let table = $("#students").DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "autoWidth": false,
                "ajax": "{{ route('campus.users.students.index', $campus) }}",
                "columns": [
                    { "data": "Codigo" },
                    { "data": "Primer_nombre" },
                    { "data": "Primer_apellido" },
                    { "data": "Numero_identificacion" },
                    { "data": "Email" },
                    { "data": "Celular" }
                ],
                "dom": "<'row'<'col-sm-12'l>>" +
                       "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        "extend": 'print',
                        "text": '<i class="fas fa-print"></i> {{ __('datatable.buttons.print') }}',
                        "titleAttr": "{{ __('datatable.buttons.printTooltip') }}",
                        "className": 'btn btn-secondary',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4, 5 ],
                        },
                        "title": "{{ __('users.students.table.title', ["campus" => $campus->Nombre]) }}",
                    },
                    {
                        "extend": 'copyHtml5',
                        "text": '<i class="fas fa-copy"></i> {{ __('datatable.buttons.copy') }}',
                        "titleAttr": "{{ __('datatable.buttons.copyTooltip') }}",
                        "className": 'btn btn-secondary',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4, 5 ]
                        }
                    },
                    {
                        "extend": 'csvHtml5',
                        "text": '<i class="fas fa-file-csv"></i> {{ __('datatable.buttons.csv') }}',
                        "titleAttr": "{{ __('datatable.buttons.csvTooltip') }}",
                        "className": 'btn btn-success',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4, 5 ]
                        },
                        "title": "{{ __('users.students.table.title', ["campus" => $campus->Nombre]) }}",
                        "action": exportAction,
                    },
                    {
                        "extend": 'excelHtml5',
                        "text": '<i class="fas fa-file-excel"></i> {{ __('datatable.buttons.excel') }}',
                        "titleAttr": "{{ __('datatable.buttons.excelTooltip') }}",
                        "className": 'btn btn-success',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4, 5 ]
                        }
                    },
                    {
                        "extend": 'colvis',
                        "text": '<i class="fas fa-columns"></i> {{ __('datatable.buttons.colvis') }}',
                        "titleAttr": "{{ __('datatable.buttons.colvisTooltip') }}",
                        "className": 'btn btn-secondary',
                        "columns": ':not(:first-child)'
                    }
                ],
                "language": {
                    "url": '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                }
            });
        });
    </script>
@stop