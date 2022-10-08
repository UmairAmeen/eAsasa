@php
$remaining_qty = calculateStockById($stock->product->id);
if($stock->warehouse_id)
{
    $warehouse_stock = warehouse_stock($stock->product->id, $stock->warehouse_id);
}
@endphp
<table  style="width:100%;padding:1px" border="0.5px" cellpadding="5">
  <tr>
      <td class="table-empty" colspan="6" style="text-align: left"><b>Total Remaining Qty</b></td>
      <td align="right" colspan="2"><b>{{$remaining_qty}}</b></td>
  </tr>
  @if($stock->warehouse_id)
  <tr>
      <td class="table-empty" colspan="6" style="text-align: left"><b>Warehouse Stock</b></td>
      <td align="right" colspan="2"><b>{{$warehouse_stock}}</b></td>
  </tr>
  @endif
</table>


