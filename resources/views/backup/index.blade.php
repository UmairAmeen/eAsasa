@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fas fa-save"></i> Backup
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="content-panel">
        <h2>Available Backup</h2>
        <form action="/backup/create" method="POST">
            <div id="log"></div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button class="btn btn-success">Generate Now</button>

        </form>
        <br>
        <br>
        <ul class="list-group">
        @foreach ($files as $val)
        @if($val != "." && $val != "..")
           <li class="list-group-item"><a href='/backup/download/{{base64_encode($path."/".$val)}}'>{{$val}}</a></li>
           @endif
           @endforeach 
        </ul>
            </div>

        </div>
    </div>

@endsection