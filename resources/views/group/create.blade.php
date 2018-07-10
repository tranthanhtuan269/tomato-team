@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <create-group :initial-users="{{ $users }}"></create-group>
        </div>
    </div>
</div>
@endsection
