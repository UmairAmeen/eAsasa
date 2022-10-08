@extends('layout')
@section('css')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endsection
@section('header')
    <div class="page-header">
        <h1><i class="glyphicon glyphicon-plus"></i> ProductGroups / Create </h1>
    </div>
@endsection

@section('content')
    @include('error')

    <div class="row">
        <form action="{{ route('product_groups.store') }}" method="POST" class="no-ajax">
        <div class="col-md-8">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="well well-sm">
                    <label>Product Group Name</label>
                    <input type="text" class="form-control" name="name">
                </div>
                <div class="well well-sm">
                    <label>Group Price</label>
                    <input type="numeric" min="0" step="0.01" class="form-control" name="price" id="pricing">
                    <small>As you select products, Pricing will be updated but you can edit as your requirements</small>
                </div>

                <div class="well well-sm">
                    <label>Product Selection</label>
                    <select name="productsSelect[]" multiple="multiple" class="form-control" id="product"></select>
                </div>
                <div id="pkr"></div>
                <div class="well well-sm">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a class="btn btn-link pull-right" href="{{ route('product_groups.index') }}"><i class="glyphicon glyphicon-backward"></i> Back</a>
                </div>
                
            </div>
            <div class="col-md-4">
                <table id="cf" class="table">
                    <thead>
                        <th>Product</th>
                        <th>Quantity</th>
                    </thead>
                    <tbody id="ppp">
                        
                    </tbody>
                </table>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('products.json')}}"></script>
  <script type="text/javascript">
    $("document").ready(function(){
        choices("#product");

    });




    function choices(identifier){
      // var warehouse_id = $("#warehouse_d").val();
          $(identifier).select2({
            placeholder: "Select a product",
            data:products_json_d,
            
            matcher: function (params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            keywords=(params.term).split(" ");

            for (var i = 0; i < keywords.length; i++) {
                if ((((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) && (((data.barcode).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) )  
                return null;
            }
            return data;
        },
            minimumInputLength: 0,
        });
              $(identifier).on('select2:select', function (e) {

                console.log(e);
              var quantity = prompt("quantity");
              var yup = "";
              var sid =  e.params.data.id;
              var pid =  e.params.data.price;
              if (quantity && quantity > 0)
              {
                yup = "<input type='hidden' class='micro' name='quantity_old[]' value='"+quantity+"' data-id='"+sid+"' data-price='"+pid+"' >";
              }else{
                yup = "<input type='hidden' class='micro' name='quantity_old[]' value='1'  data-id='"+sid+"' data-price='"+pid+"' >";
              }
              $("#pkr").append(yup);
              updatePricing();
        });


              $(identifier).on('select2:unselect', function (e) {
              var sid = e.params.data.id;
              $("input[data-id="+sid+"]").remove();
               updatePricing();
        });

      }

      function updatePricing()
      {
        var price = 0;

        $.each($(".micro"), function(index, data){
            price += $(data).val() * $(data).data("price");
        });

        $("#pricing").val(price);
        updatePreview();
      }

       function updatePreview()
      {
        var js = "";
        $.each($(".micro"), function(index, data){
            var pr = 0;
            var my_id = $(data).data("id");
            $.each(products_json_d, function(i, d){
                if (d.id == my_id)
                {
                   js +="<tr><td>#"+d.id+" "+d.text+"<input type='hidden' name='products[]' value='"+d.id+"'><input type='hidden' name='quantity[]' value='"+$(data).val()+"'></td><td>"+$(data).val()+"</td></tr>";
                }
            });
        });
        $("#ppp").html(js);
      }
  </script>
@endsection
