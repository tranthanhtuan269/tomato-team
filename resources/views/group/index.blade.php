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
            @if(Auth::user()->permission == 0)
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
                        <tr class="{{ $group->status == 0 ? 'info' : 'success' }}">
                            <td>{{ $group->name }}</td>
                            <td><?php echo $group->users; ?></td>
                            <td>{{ $group->status == 0 ? 'Đang dịch...' : 'Đã dịch xong' }}</td>
                            <td>{{ $group->created_at }}</td>
                            <td>
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
                    {{ $group->name }}
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
