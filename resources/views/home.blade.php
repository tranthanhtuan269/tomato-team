@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($groups as $group)
        <?php 
            $date1 = new DateTime($group->created_at);
            $date2 = new DateTime("now");
            $interval = $date2->diff($date1);
        ?>
            <div class="col-sm-3 group-holder">
                @if($group->status == 0)
                <div class="panel panel-primary">
                @elseif($group->status == 1)
                <div class="panel panel-success">
                @endif
                    <div class="panel-heading">{{ $group->name }} <span class="pull-right">{{ $interval->format("%Hh : %Im") }}</span></div>
                    <div class="panel-body"><div class="btn btn-danger"><a href="{{ url('/') }}/groups/{{ $group->id }}">Join</a></div></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
