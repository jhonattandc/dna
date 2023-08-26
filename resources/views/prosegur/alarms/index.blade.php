@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>{{ __('prosegur.alarms.title') }}</h1>
@stop

@section('content')
    <div class="card">
        <div id="wrapper" class="card-body">
            <table id="alarms" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('prosegur.alarms.table.headers.system') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.location') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.event') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.operator') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.triggered_at') }}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>{{ __('prosegur.alarms.table.headers.system') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.location') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.event') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.operator') }}</th>
                        <th>{{ __('prosegur.alarms.table.headers.triggered_at') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop

{{-- @section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop --}}

@section('js')
    <script>
        $(function () {
            /* For Export Buttons available inside jquery-datatable "server side processing" - Start
            - due to "server side processing" jquery datatble doesn't support all data to be exported
            - below function makes the datatable to export all records when "server side processing" is on */

            function newExportAction(e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;
                dt.one('preXhr', function (e, s, data) {
                    // Just this once, load all data from the server...
                    data.start = 0;
                    data.length = dt.page.info().recordsTotal;
                    dt.one('preDraw', function (e, settings) {
                        // Call the original action function
                        if (button[0].className.indexOf('buttons-copy') >= 0) {
                            $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                        } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                            $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                        } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                            $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                                $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                        } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                            $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                        } else if (button[0].className.indexOf('buttons-print') >= 0) {
                            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                        }
                        dt.one('preXhr', function (e, s, data) {
                            // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                            // Set the property to what it was before exporting.
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                        });
                        // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                        setTimeout(dt.ajax.reload, 0);
                        // Prevent rendering of the full data to the DOM
                        return false;
                    });
                });
                // Requery the server with the new one-time export settings
                dt.ajax.reload();
            };
            //For Export Buttons available inside jquery-datatable "server side processing" - End

            let table = $("#alarms").DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "autoWidth": false,
                "ajax": "{{ route('prosegur.alarms.index') }}",
                "columns": [
                    { "data": "system" },
                    { "data": "location" },
                    { "data": "event" },
                    { "data": "operator" },
                    { "data": "triggered_at" }
                ],
                "order": [[ 4, "desc" ]],
                "dom": "<'row'<'col-sm-12'l>>" +
                       "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        "extend": 'print',
                        "text": '<i class="fas fa-print"></i> {{ __('prosegur.alarms.table.buttons.print') }}',
                        "titleAttr": "{{ __('prosegur.alarms.table.buttons.print') }}",
                        "className": 'btn btn-secondary',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4 ],
                        },
                        "title": "{{ __('prosegur.alarms.table.title') }}",
                    },
                    {
                        "extend": 'copyHtml5',
                        "text": '<i class="fas fa-copy"></i> {{ __('prosegur.alarms.table.buttons.copy') }}',
                        "titleAttr": "{{ __('prosegur.alarms.table.buttons.copy') }}",
                        "className": 'btn btn-secondary',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4 ]
                        }
                    },
                    {
                        "extend": 'csvHtml5',
                        "text": '<i class="fas fa-file-csv"></i> {{ __('prosegur.alarms.table.buttons.csv') }}',
                        "titleAttr": "{{ __('prosegur.alarms.table.buttons.csv') }}",
                        "className": 'btn btn-success',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4 ]
                        },
                        "title": "{{ __('prosegur.alarms.table.title') }}",
                        "action": newExportAction,
                    },
                    {
                        "extend": 'excelHtml5',
                        "text": '<i class="fas fa-file-excel"></i> {{ __('prosegur.alarms.table.buttons.excel') }}',
                        "titleAttr": "{{ __('prosegur.alarms.table.buttons.excel') }}",
                        "className": 'btn btn-success',
                        "exportOptions": {
                            "columns": [ 0, 1, 2, 3, 4 ]
                        }
                    },
                    {
                        "extend": 'colvis',
                        "text": '<i class="fas fa-columns"></i> {{ __('prosegur.alarms.table.buttons.colvis') }}',
                        "titleAttr": "{{ __('prosegur.alarms.table.buttons.colvis') }}",
                        "className": 'btn btn-secondary',
                        "columns": ':not(:first-child)'
                    }
                ],
            });
        });
    </script>
@stop