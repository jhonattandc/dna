@extends('adminlte::page')

@section('title', __('users.title'))

@section('content_header')
    <h1>{{ __('users.header') }}</h1>
@stop

@section('content')
    <div class="card card-solid">
        <div class="card-header">
            <div class="card-title">{{ __('users.title') }}</div>
            <div class="text-right">
                <div class="btn-group">
                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-user-plus"></i> {{ __('users.register.button') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body pb-0">
            <div class="row">
                @foreach ($users as $user)
                    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                        <div class="card bg-light d-flex flex-fill">
                            <div class="card-header text-muted border-bottom-0">
                                {{ $user->permissions->count() }} {{ __('users.permissions.label') }}
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="lead"><b>{{ $user->name }}</b></h2>
                                        <ul class="ml-4 mb-0 fa-ul text-muted">
                                            <li class="small"><span class="fa-li"><i
                                                        class="fas fa-lg fa-inbox"></i></span>
                                                {{ __('users.email.label') }}: {{ $user->email }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-right">
                                    <div class="btn-group">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-user-edit"></i> {{ __('users.edit.button') }}
                                        </a>
                                        <a class="btn btn-sm btn-danger" data-id="{{ $user->id }}" data-action="{{ route('users.destroy',$user->id) }}" onclick="deleteUser({{$user->id}})">
                                            <i class="fas fa-user-times"></i> {{ __('users.delete.button') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-footer">
            <nav aria-label="Contacts Page Navigation">
                <ul class="pagination justify-content-center m-0">
                    {{ $users->links() }}
                </ul>
            </nav>
        </div>

    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteUser(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: '{{ __('users.delete.title') }}',
                text: '{{ __('users.delete.text') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('users.delete.confirm') }}',
                cancelButtonText: '{{ __('users.delete.cancel') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: '{{url('/users')}}/' + id,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            Swal.fire(
                                '{{ __('users.delete.success.title') }}',
                                '{{ __('users.delete.success.text') }}',
                                'success'
                            ).then((result) => {
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                '{{ __('users.delete.error.title') }}',
                                '{{ __('users.delete.error.text') }}',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@stop
