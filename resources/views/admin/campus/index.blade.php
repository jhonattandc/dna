@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Panel de control</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sedes</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm">
                                    <a class="btn btn-dark"  href="{{ route('home.create') }}">Agregar</a>
                                </div>
                            </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Secreto</th>
                                    <th width="50px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campus as $campus)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $campus->Nombre }}</td>
                                        <td>
                                            <div class="input-group">
                                                <input id="secret-{{ $campus->id }}" type="password" value={{ $campus->Secreto }} class="form-control" disabled="">
                                                <span class="input-group-append">
                                                    <button class="btn btn-sm btn-dark btn-flat" type="button"  onclick="show_secret({{ $campus->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <a class="btn btn-dark" href="{{ route('home.show',$campus->id) }}" role="button">Ver más</a>
                                            <a class="btn btn-secondary" href="{{ route('home.edit',$campus->id) }}" role="button">Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- @section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop --}}

@section('js')
    <script>
        function show_secret(input_id) {
            var x = document.getElementById("secret-"+input_id);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
@stop
