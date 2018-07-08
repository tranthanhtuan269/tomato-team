@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <list-group :initial-groups="{{ $groups }}"></list-group>
        </div>
    </div>
</div>
@endsection
