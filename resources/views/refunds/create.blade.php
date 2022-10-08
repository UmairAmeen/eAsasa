@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> Refund / Add Claim </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <div class="col-md-12">

            <form action="{{ route('refunds.store') }}" method="POST">
            <div id="log"></div>
             <div class="form-group">
                <label>Date:</label>
                <input type="text" name="date" class="form-control date-picker">
                 <small id="emailHelp" class="form-text text-muted">Claim Date</small>
            </div>
            <div class="form-group">
                <label>Product:</label>
                <select name="product" class="form-control product"></select>
                 <small id="emailHelp" class="form-text text-muted">Which Product to be claimed</small>
            </div>
            <div class="form-group">
                <label>Customer:</label>
                <select name="customer" class="form-control customer">
                    <option value="">No Customer Claim</option>
                </select>
                 <small id="emailHelp" class="form-text text-muted">Select if this claim is from customer</small>
            </div>


            <div class="form-group">
                <label>Supplier:</label>
                <select name="supplier" class="form-control supplier">
                    <!-- <option>No Supplier Claim</option> -->
                </select>
                 <small id="emailHelp" class="form-text text-muted">Which Supplier is responsible for claim</small>
            </div>

            <div class="form-group">
                <label>Warehouse:</label>
                <select name="warehouse" class="form-control warehouse"></select>
                 <small id="emailHelp" class="form-text text-muted">From Which Warehouse Stock should be removed</small>
            </div>

            <div class="form-group">
                <label>Stock:</label>
                <input type="number" min="0" name="stock" class="form-control" placeholder="Stock">
                 <small id="emailHelp" class="form-text text-muted">Total Quantity to be removed</small>
            </div>

            <div class="form-group">
                <label>Supplier Price per pcs:</label>
                <input type="text" name="price" class="form-control" placeholder="Price">
                 <small id="emailHelp" class="form-text text-muted">Product Price to be returned per pcs to supplier</small>
            </div>

            <div class="form-group">
                <label>Claim Description:</label>
                <textarea name="description" class="form-control"></textarea>
                 <small id="emailHelp" class="form-text text-muted">Claim Desciption (if any)</small>
            </div>


                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('notifications.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
  <script src="{{asset('/assets/js/tinymce.min.js')}}"></script>

<script type="text/javascript">
$(document).ready(function() {

tinymce.init({ selector:'textarea',setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });} });

  var date = new Date();
var day = date.getDate();
var monthIndex = date.getMonth() + 1;
var year = date.getFullYear();

$('.date-picker').val(day+"-"+monthIndex+"-"+year);// = new Date();

  $(".supplier").select2({
         ajax: {
          url: "/supplier_json",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              // page: params.page
            };
          },
          processResults: function (data, params) {
            return {
              results: data,
             
            };
          },
          cache: true
        },
        minimumInputLength: 0
    });


  $(".product").select2({
         ajax: {
          url: "/pagination_product_json/",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 10) < data.total_count
              }
            };
          },
          cache: true
        },
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });

    $(".customer").select2({
         ajax: {
          url: "/pagination_customer_json/",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // parse the results into the format expected by Select2
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data, except to indicate that infinite
            // scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.items,
              pagination: {
                more: (params.page * 10) < data.total_count
              }
            };
          },
          cache: true
        },
        // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 0,
    });

$(".warehouse").select2({
         ajax: {
          url: "/allwarehousejson",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              // page: params.page
            };
          },
          processResults: function (data, params) {
            return {
              results: data,
             
            };
          },
          cache: true
        },
        minimumInputLength: 0
    });
});
</script>
@endsection

