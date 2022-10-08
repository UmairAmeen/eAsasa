@extends('layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css"
        rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-edit"></i> StockAdjustments / Edit #{{ $stock_adjustment->id }}</h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <h3>Product Name: {{ $stock_adjustment->product->name }}</h3>
            <h3>Warehouse Name: {{ $stock_adjustment->warehouse->name }}</h3>
            <form action="{{ route('stock.update', $stock_adjustment->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div id="log" class=""></div>

                <div class="form-group">
                    <label>Date:</label>
                    <input type="text" name="date" value="{{ date('m/d/Y', strtotime($stock_adjustment->date)) }}"
                        class="form-control date-picker" placeholder="Stock Adjustment Date">
                    <small id="emailHelp" class="form-text text-muted">Date When Stock is Loaded/Unloaded</small>
                </div>
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="text" name="quantity" value="{{ $stock_adjustment->quantity }}" class="form-control"
                        placeholder="Product Quantity">
                    <small id="emailHelp" class="form-text text-muted">Quantity!</small>
                </div>
                <div class="form-group">
                    <label>Notes:</label>
                    <input type="text" name="notes" value="{{ $stock_adjustment->notes ? : '-' }}" class="form-control"
                        placeholder="N  otes">
                    <small id="emailHelp" class="form-text text-muted">Notes!</small>
                </div>
                <div class="form-group">
                    <label>Warehouse:</label>
                    <select class="form-control" name="warehouse">
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ is_selected($warehouse->id, $stock_adjustment->warehouse_id) }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    <small id="emailHelp" class="form-text text-muted">Warehouse related in this stock</small>
                </div>


                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a class="btn btn-link pull-right" href="{{ route('stock.index') }}"><i
                            class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.date-picker').datepicker({});
    </script>
@endsection
