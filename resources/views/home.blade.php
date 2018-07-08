@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    	@if(false)
        <div class="col-sm-6">
            <create-group :initial-users="{{ $users }}"></create-group>
        </div>
        @endif
        <div class="col-sm-6">
            <groups :initial-groups="{{ $groups }}" :user="{{ $user }}"></groups>
        </div>
    </div>
</div>
@endsection
