@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12">
            @if(Auth::user()->permission == 1)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Channel Name</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                        <tr class="{{ $group->status == 0 ? 'info' : 'success' }}" id="group-{{ $group->id }}">
                            <td>{{ $group->name }}</td>
                            <td><?php echo $group->user_info; ?></td>
                            <td>
                                @if($group->status == 0) Đang dịch... @endif
                                @if($group->status == 1) Dịch xong @endif
                                @if($group->status == 2) Đã sửa... @endif
                                @if($group->status == 3) Sửa xong @endif
                            </td>
                            <td>{{ $group->created_at }}</td>
                            <td>
                                <a class="btn btn-small btn-success" href="{{ URL::to('groups/' . $group->id) }}">View</a>
                                {{ Form::open(array('url' => 'groups/' . $group->id, 'class' => 'pull-right')) }}
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    {{ Form::submit('Delete', array('class' => 'btn btn-warning')) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach 
                </tbody>
            </table>
            @else
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
                            <div class="panel-body"><a href="{{ url('/') }}/groups/{{ $group->id }}"><div class="btn btn-danger">Join</div></a></div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
