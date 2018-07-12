@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <create-group :initial-users1="{{ $users }}" :initial-users2="{{ $users }}"></create-group>
    </div>
</div>
@endsection
