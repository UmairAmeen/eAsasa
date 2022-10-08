@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-align-justify"></i> ProductCategories
            <a class="btn btn-success pull-right" href="{{ route('product_categories.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a>
        </h1>

    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if($product_categories->count())
            <div class="content-panel">
            <div class="table-responsive">

                <table class="table table-bordered table-condensed table-hover table-striped products_listing table-responsive">
                                                  <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($product_categories as $product_category)
                            <tr>
                                <td>{{$product_category->id}}</td>
                                <td>{{$product_category->name}}</td>
                                <td>{{$product_category->description}}</td>
                                <td class="text-right">
                                    @if($product_category->id != 1)
                                    <a class="btn btn-xs btn-warning" href="{{ route('product_categories.edit', $product_category->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('product_categories.destroy', $product_category->id) }}" method="POST" style="display: inline;" >
                                        <div id="log"></div>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="if(confirm('Delete? Are you sure?')) { return true } else {return false };"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
                {!! $product_categories->render() !!}
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>

@endsection