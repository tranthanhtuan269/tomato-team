@extends('layouts.app')

@section('content')
<div class="container-fuild">
    <div class="row">
        <div class="col-sm-12">
            <groups :initial-groups="{{ $groups }}" :user="{{ $user }}"></groups>
        </div>
    </div>
</div>
@endsection
