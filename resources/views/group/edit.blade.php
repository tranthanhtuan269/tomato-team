@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <edit-group :group="{{ $group }}" :joined="{{ $users }}"></edit-group>
    </div>
</div>
@endsection
