@extends('layout')
@section('header')
<div class="page-header">
        <h1>Users / Show #{{$user->id}}</h1>
        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" >
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-warning btn-group" role="group" href="{{ route('users.edit', $user->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                <button type="submit" class="btn btn-danger" onclick="return (confirm('Delete? Are you sure?'))">Delete <i class="glyphicon glyphicon-trash"></i></button>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
              <ul class="list-group list-group-flush">
                <li class="list-group-item"><b>Name:</b> {{$user->name}}</li>
                <li class="list-group-item"><b>Roles:</b> 
                    @if(!empty($user->roles))
                        @foreach($user->roles as $v)
                            <label class="label label-success">{{ $v->display_name }}</label>
                        @endforeach
                    @endif
                </li>
              </ul>
            </div>


            <a class="btn btn-link" href="{{ route('users.index') }}"><i class="glyphicon glyphicon-backward"></i>  Back</a>

        </div>
    </div>

@endsection