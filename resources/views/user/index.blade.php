@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if (Session::has('message'))
            <div class="alert alert-info">
                {{ Session::get('message') }}
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>s
                            <td>{{ $user->created_at }}</td>
                            <td>
                                {{ Form::open(array('url' => 'users/' . $user->id, 'class' => 'pull-right')) }}
                                    {{ Form::hidden('_method', 'DELETE') }}
                                    <a class="btn btn-small btn-info" href="{{ URL::to('users/' . $user->id . '/edit') }}">Edit</a>
                                    {{ Form::submit('Delete', array('class' => 'btn btn-warning')) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach 
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
