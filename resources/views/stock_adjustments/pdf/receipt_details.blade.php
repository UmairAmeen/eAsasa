<style>
  .name{
    font-size: 11px;
  }
  .stock_detail{
    font-size: 15px;
  }
  td {
    display: flex;
    align-items: center;
    vertical-align: middle;
}
</style>
<table class="stock_detail_table">
    <tr>
        <td width="25%" class="stock_detail"><strong>Stock # :</strong></td>
        <td width="25%" class="stock_detail">{{ $stock->id }}</td>
    </tr>
    @if($stock->purchase_id)
    <tr>
        <td width="25%" class="stock_detail"><strong>Purchase ID :</strong></td>
        <td width="25%" class="stock_detail">{{ $stock->purchase_id }}</td>
    </tr>
    @endif
    @if($stock->sale_id)
    <tr>
        <td width="25%" class="stock_detail"><strong>Sale ID :</strong></td>
        <td width="25%" class="stock_detail">{{ $stock->sale_id }}</td>
    </tr>
    @endif
    @if($stock->sale_orders_id)
    <tr>
        <td width="25%" class="stock_detail"><strong>Sale Order ID :</strong></td>
        <td width="25%" class="stock_detail">{{ $stock->sale_orders_id }}</td>
    </tr>
    @endif
    @if($stock->refund_id)
    <tr>
        <td width="25%"><strong>Refund ID :</strong></td>
        <td width="25%">{{ $stock->refund_id }}</td>
    </tr>
    @endif
    <tr>
        <td width="25%"><strong>Stock Type :</strong></td>
        <td width="25%">{{ ucwords($stock->type) }}</td>
        <td width="20%"></td>
        <td width="15%"><strong>Date :</strong></td>
        <td width="15%">{{ $stock->date }}</td>
    </tr>
</table>
<br><br>
<table border="0" width="100%" cellpadding="1" cellspacing="1"  style="background-color:#d3d3d3">
    <tr>
        <td></td>
    </tr>
  <tr class="header-height">
      <td width="2%"></td>
      @if($stock->supplier)
      <td width="46%"><h3>Supplier:</h3><br><strong class="name">{{ $stock->supplier->name }}</strong><br>{{ $stock->supplier->phone }}
      <br><br><i><b>{{ $stock->supplier->address }}</b></i>
      </td>
      @elseif($stock->customer)
      <td width="46%"><h3>Customer:</h3><br><strong class="name">{{ $stock->customer->name }}</strong><br>{{ $stock->customer->phone }}
      <br><br><i><b>{{ $stock->customer->address }}</b></i>
      </td>
      @elseif(!$stock->customer && !$stock->supplier && $stock->warehouse)
      <td width="46%"><h3>Warehouse:</h3><br><strong class="name">{{ $stock->warehouse->name }}</strong>
      <br><br><i><b>{{ $stock->warehouse->address }}</b></i>
      </td>
      @endif
      <td width="2%"></td>
      @if(($stock->supplier || $stock->customer) && $stock->warehouse)
      <td width="46%"><h3>Warehouse:</h3><br><strong class="name">{{ $stock->warehouse->name }}</strong>
      <br><br><i><b>{{ $stock->warehouse->address }}</b></i>
      </td>
      @endif
  </tr>
  <tr>
      <td></td>
  </tr>
</table>
@if($stock->product)
@php
$remaining_qty = calculateStockById($stock->product->id);
@endphp
  <h3>Product Details:</h3>
  <table cellpadding="5" style="width:100%;padding:1px" border="0.5px">
      <thead>
          <tr style="background-color: black;color:white">
              <th align="center">SR#</th>
              @if($stock->batch_id)
              <th align="center">Batch#</th>
              @endif
              <!-- @if($stock->product->barcode) -->
              <th align="center">Barcode</th>
              <!-- @endif -->
              @if($stock->product->image_path)
              <th align="center">Image</th>
              @endif
              <th align="center">Name</th>
              <th align="center">Brand Name</th>
              <!-- @if ($stock->product->description) -->
              <th colspan="3" align="center">Description</th>
              <!-- @endif -->
              @if($stock->product->color)
              <th style="text-align: center">Color</th>
              @endif
              @if($stock->product->size)
              <th style="text-align: center">Size</th>
              @endif
              <th style="text-align: center"><b>Qty</b></th>
              <th style="text-align: center"><b>Remaining Qty</b></th>
          </tr>
      </thead>
      <tbody style="border-bottom: 5px solid black;">

              <tr>
                  <td class="id" align="center">1.</td>
                  @if($stock->batch_id)
                  <td align="center">{{ $stock->batch_id }}</td>
                  @endif
                  <!-- @if($stock->product->barcode) -->
                  <td align="center">{{ $stock->product->barcode?:'-' }}</td>
                  <!-- @endif -->
                  @if($stock->product->image_path)
                  <td align="center">
                    <img src="{{ 'data:image/jpeg;base64,'.base64_encode(file_get_contents(storage_path('app/public/'.$stock->product->image_path.''))) }}" width="60px" height="60px" alt="abc">
                  </td>
                  @endif
                  <td align="center">{{ $stock->product->name }}</td>
                  <td align="center">{{ $stock->product->brand?:'-' }}</td>
                  <!-- @if ($stock->product->description) -->
                      <td colspan="3" align="center">{{ $stock->product->description?:'-' }}</td>
                  <!-- @endif -->
                  @if($stock->product->color)
                      <td align="center">{{ $stock->product->color }}</td>
                  @endif
                  @if($stock->product->size)
                  <td align="center">{{ $stock->product->size }}</td>
                  @endif
                  <td align="center">{{ $stock->quantity }}</td>
                  <td align="center">{{$remaining_qty}}</td>
              </tr>
      </tbody>
  </table>
@endif