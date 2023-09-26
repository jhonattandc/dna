@extends('adminlte::page')

@section('title', __('users.register.title'))

@section('content_header')
    <h1>{{ __('users.register.header') }}</h1>
@stop

@section('content')
    <div class="row">

        <div class="col-md-4 mx-auto">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('users.register.title') }}</h3>
                </div>
                <form id="createForm" action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('users.name.label') }}</label>
                            <input id="name" name="name" type="text" class="form-control"
                                placeholder="{{ __('users.name.placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label for="email">{{ __('users.email.label') }}</label>
                            <input id="email" name="email" type="email" class="form-control"
                                placeholder="{{ __('users.email.placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label for="password">{{ __('users.password.label') }}</label>
                            <input id="password" name="password" type="password" class="form-control"
                                placeholder="{{ __('users.password.placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">{{ __('users.password_confirmation.label') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="form-control" placeholder="{{ __('users.password_confirmation.placeholder') }}">
                        </div>
                        {{-- Multiselection item to mark all permisions to attach --}}
                        <div class="form-group">
                            <label for="permissions">{{ __('users.permissions.label') }}</label>
                            <select id="permissions" name="permissions[]" class="form-control select2"
                                data-placeholder="{{ __('users.permissions.placeholder') }}" multiple="multiple">
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-secondary">{{ __('users.register.submit') }}</button>
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
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
    <script>
        $(function() {
            $('.select2').select2()

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            $('#createForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3,
                        maxlength: 255
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        maxlength: 255
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 8,
                        maxlength: 255,
                        equalTo: "#password"
                    }
                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('users.name.label')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('users.name.label'), 'min' => 3]) }}",
                        maxlength: "{{ __('validation.max.string', ['attribute' => __('users.name.label'), 'max' => 255]) }}"
                    },
                    email: {
                        required: "{{ __('validation.required', ['attribute' => __('users.email.label')]) }}",
                        email: "{{ __('validation.email', ['attribute' => __('users.email.label')]) }}",
                        maxlength: "{{ __('validation.max.string', ['attribute' => __('users.email.label'), 'max' => 255]) }}"
                    },
                    password: {
                        required: "{{ __('validation.required', ['attribute' => __('users.password.label')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('users.password.label'), 'min' => 8]) }}",
                        maxlength: "{{ __('validation.max.string', ['attribute' => __('users.password.label'), 'max' => 255]) }}"
                    },
                    password_confirmation: {
                        required: "{{ __('validation.required', ['attribute' => __('users.password_confirmation.label')]) }}",
                        minlength: "{{ __('validation.min.string', ['attribute' => __('users.password_confirmation.label'), 'min' => 8]) }}",
                        maxlength: "{{ __('validation.max.string', ['attribute' => __('users.password_confirmation.label'), 'max' => 255]) }}",
                        equalTo: "{{ __('validation.same', ['attribute' => __('users.password_confirmation.label'), 'other' => __('users.password.label')]) }}"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback')
                    element.closest('.form-group').append(error)
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid')
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid')
                }
            });
        })
    </script>
@stop
