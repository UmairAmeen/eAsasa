@extends('layout')
@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-user"></i> Roles
            <a class="btn btn-success pull-right" href="{{ route('roles.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
                <div class="content-panel">

            @if($roles->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{$role->id}}</td>
                                <td>{{$role->name}}</td>
                                <td>{{$role->description}}</td>
                                
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('roles.show', $role->id) }}"><i class="glyphicon glyphicon-eye-open"></i> Show</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('roles.edit', $role->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline;" >
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return (confirm('Delete? Are you sure?'))"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $roles->render() !!}
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif
            </div>
        </div>
    </div>
@endsection