@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="fas fa-balance-scale"></i> Units
            <a accesskey="n" class="btn btn-success pull-right" href="{{ route('units.create') }}"><i class="glyphicon glyphicon-plus"></i> Add More Units <br><small>ALT + N</small></a>
        </h1>

    </div>
@endsection

@section('content')
     <div class="content-panel">

    <div class="row">
        <div class="col-md-12">
            @if($units->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($units as $unit)
                            <tr>
                                <td>{{$unit->id}}</td>
                                <td>{{$unit->name}}</td>
                                <td class="text-right">
                                    <!-- <a class="btn btn-xs btn-primary" href="{{ route('units.show', $unit->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a> -->
                                    @if($unit->deleteable)
                                    <a class="btn btn-xs btn-warning" href="{{ route('units.edit', $unit->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="no-ajax" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                     </tbody>
                </table>
                {!! $units->render() !!}
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>
</div>
@endsection