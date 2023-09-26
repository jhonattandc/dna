@extends('adminlte::page')

@section('title', __('users.edit.title'))

@section('content_header')
    <h1>{{ __('users.edit.header', ['name' => $user->name]) }}</h1>
@stop

@section('content')
    <div class="row">

        <div class="col-md-4 mx-auto">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('users.edit.title') }}</h3>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('users.name.label') }}</label>
                            <input id="name" name="name" type="text" value="{{ $user->name }}" class="form-control" placeholder="{{ __('users.name.placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label for="email">{{ __('users.email.label') }}</label>
                            <input id="email" name="email" type="email" value="{{ $user->email }}" class="form-control" placeholder="{{ __('users.email.placeholder') }}">
                        </div>
                        {{-- Multiselection item to mark all permisions to attach --}}
                        <div class="form-group">
                            <label for="permissions">{{ __('users.permissions.label') }}</label>
                            <select id="permissions" name="permissions[]" class="form-control select2" data-placeholder="{{ __('users.permissions.placeholder') }}" multiple="multiple">
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}" {{ $user->permissions->contains($permission->id) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-secondary">{{ __('users.edit.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
@stop

@section('js')
    <script>
        $(function(){
            $('.select2').select2()

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        })
    </script>
@stop
