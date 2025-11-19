@extends('voyager::master')

@section('page_title', (isset($person->id) ? 'Editar' : 'Añadir').' Persona')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-person"></i>
        {{ (isset($person->id) ? 'Editar' : 'Añadir').' Persona' }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form"
              action="{{ isset($person->id) ? route('admin.people.update', $person->id) : route('admin.people.store') }}"
              method="POST" enctype="multipart/form-data" autocomplete="off">

            @if(isset($person->id))
                @method('PUT')
            @endif
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="first_name">Primer Nombre</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name', $person->first_name ?? '') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="middle_name">Segundo Nombre</label>
                                    <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ old('middle_name', $person->middle_name ?? '') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="paternal_surname">Apellido Paterno</label>
                                    <input type="text" class="form-control" name="paternal_surname" id="paternal_surname" value="{{ old('paternal_surname', $person->paternal_surname ?? '') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="maternal_surname">Apellido Materno</label>
                                    <input type="text" class="form-control" name="maternal_surname" id="maternal_surname" value="{{ old('maternal_surname', $person->maternal_surname ?? '') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="ci">CI/Pasaporte</label>
                                    <input type="text" class="form-control" name="ci" id="ci" value="{{ old('ci', $person->ci ?? '') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="birth_date">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" name="birth_date" id="birth_date" value="{{ old('birth_date', $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="gender">Género</label>
                                    <select name="gender" id="gender" class="form-control select2">
                                        <option value="" @if(old('gender', $person->gender) == '') selected @endif>No especificado</option>
                                        <option value="Masculino" @if(old('gender', $person->gender) == 'Masculino') selected @endif>Masculino</option>
                                        <option value="Femenino" @if(old('gender', $person->gender) == 'Femenino') selected @endif>Femenino</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $person->email ?? '') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">Teléfono/Celular</label>
                                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $person->phone ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Dirección</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $person->address ?? '') }}</textarea>
                            </div>

                            @if (isset($person->id))
                                <div class="form-group">
                                    <label for="status">Estado</label> <br>
                                    <input type="hidden" name="status" value="0">
                                    <input type="checkbox" name="status" class="toggleswitch"
                                        data-on="Activo" data-off="Inactivo"
                                        value="1"
                                        {{ old('status', $person->status ?? 1) == 1 ? 'checked' : '' }}>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel panel-bordered panel-warning">
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Imagen de Perfil</label>
                                @if(isset($person->image))
                                    <img src="{{ Voyager::image($person->image) }}" style="width:100%; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
                                @endif
                                <input type="file" data-name="image" name="image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary pull-right save">
                {{ __('voyager::generic.save') }}
            </button>
        </form>
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();
        });
    </script>
@stop
