@extends('layout')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-align-justify"></i> ProductGroups
            @if (is_allowed('product-create'))
            <a class="btn btn-success pull-right" href="{{ route('product_groups.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a>
            @endif
        </h1>
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if($product_groups->count())
                <table class="table table-condensed table-striped datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Group Name</th>
                            <th>Group Price</th>
                            <th>Product Details</th>
                            <th class="text-right">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($product_groups as $product_group)
                            <tr>
                                <td>{{$product_group->id}}</td>
                                <td>{{$product_group->name}}</td>
                                <td>{{number_format($product_group->price)}}</td>
                                <td><?php $pro = unserialize($product_group->products); $qty = unserialize($product_group->quantity); ?>
                                    
                                    <table class="table">
                                        <thead>
                                            <th>Product Detail</th>
                                            <th>Quantity</th>
                                        </thead>
                                        <tbody>
                                            @foreach($pro as $key=> $pdo)
                                            <?php $product = get_product($pdo) ?>
                                            <tr>
                                                <td>#{{$product->id}} {{$product->name}}  {{$product->brand}}</td>
                                                <td>{{$qty[$key]}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>


                                </td>
                                
                                <td class="text-right">
                                    {{-- <a class="btn btn-xs btn-primary" href="{{ route('product_groups.show', $product_group->id) }}"><i class="glyphicon glyphicon-eye-open"></i> View</a> --}}
                                    <a class="btn btn-xs btn-warning" href="{{ route('product_groups.edit', $product_group->id) }}"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="{{ route('product_groups.destroy', $product_group->id) }}" method="POST" style="display: inline;" onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>

@endsection