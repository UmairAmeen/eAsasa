@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fa fa-warehouse"></i> Warehouses
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('warehouses.create') }}"><i class="glyphicon glyphicon-plus"></i> Create <br><small>ALT + N</small></a>
        </h1>

    </div>
@endsection

@section('content')
      <div class="content-panel">

    <div class="row">
        <div class="col-md-12">
            @if($warehouses->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($warehouses as $warehouse)
                            <tr>
                                <td>{{$warehouse->id}}</td>
                                <td>{{$warehouse->name}}</td>
                                <td>{{($warehouse->address)?$warehouse->address:"N/A"}}</td>
                                
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('warehouses.show', $warehouse->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View Stock Information</a>
                                    <a class="btn btn-xs btn-warning" href="{{ route('warehouses.edit', $warehouse->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{route('warehouses.destroy', $warehouse->id)}}" method="POST" style="display: inline;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete? This will delete all related stock logs?')"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $warehouses->render() !!}
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>
</div>
@endsection