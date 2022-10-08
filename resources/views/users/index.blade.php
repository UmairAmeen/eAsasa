@extends('layout')
@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-user"></i> Users
            <a class="btn btn-success pull-right" href="{{ route('users.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
                <div class="content-panel">

            @if($users->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            @if (is_admin())
                            <th>Discount(%)</th>
                            @endif
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    @if(!empty($user->roles))
                                        @foreach($user->roles as $v)
                                            <label class="label label-success">{{ $v->display_name }}</label>
                                        @endforeach
                                    @endif
                                </td>
                                @if (is_admin())
                                <td>{{$user->allowed_discount}}</td>
                                @endif
                                <td class="text-right">
                                    @if(!$user->hasRole('Admin'))
                                    <a class="btn btn-xs btn-primary" href="{{ route('users.show', $user->id) }}"><i class="glyphicon glyphicon-eye-open"></i> Show</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('users.edit',$user->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" >
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return (confirm('Delete? Are you sure?'))"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $users->render() !!}
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
            </div>
        </div>
    </div>
@endsection