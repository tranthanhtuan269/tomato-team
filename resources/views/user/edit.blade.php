@extends('layouts.app')

@section('content')
<div class="container">

<h1>Edit {{ $user->name }}</h1>

<!-- if there are creation errors, they will show here -->
@if(count($errors->all()) > 0)
    <div class="alert alert-danger" role="alert">
        {{ Html::ul($errors->all()) }}
    </div>
@endif


{{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}

    <div class="form-group">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', Request::old('name'), array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('email', 'Email') }}
        {{ Form::email('email', Request::old('email'), array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('password', 'Password') }}
        <input type="password" name="password" class="form-control" placeholder="Password" value="<?php echo Request::old('password'); ?>">
    </div>

    <div class="form-group">
        {{ Form::label('phone', 'Phone') }}
        {{ Form::text('phone', Request::old('phone'), array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('permission', 'Permission') }}
        {!! Form::select('permission', ['1' => 'Admin', '2' => 'Normal'], null, ['class' => 'form-control', 'placeholder' => 'Select a permission']) !!}
    </div>

    <div class="form-group">
        {{ Form::label('languages', 'Language Group') }}
        {!! Form::select('languages', ['0' => 'KOREAN - VIETNAMESE', '1' => 'VIETNAMESE - ENGLISH'], 0, ['class' => 'form-control']) !!}
    </div>

    {{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}
    <a href="{{ URL::to('users') }}" class="btn btn-primary">View All</a>
{{ Form::close() }}

</div>
@endsection